# =============================================================================
# Invoke-SharePointVersionTrim.ps1
# Description : Trims file version history across all SharePoint Online sites.
#               Supports two strategies:
#                 -KeepVersions  N  – keep the N most-recent versions per file
#                 -DeleteOlderThanDays X – delete versions older than X days
#               Run with -WhatIf first to preview changes before deleting.
# Requirements: PnP.PowerShell module  (v2+)
#               Install-Module PnP.PowerShell -Force
#
# Authentication – one-time setup (run as Global Administrator):
#   .\Register-SPOVersionManagerApp.ps1 -TenantName "contoso"
#   This creates an Entra ID App Registration and prints the Client ID.
#   Pass that Client ID to this script via -ClientId on every run.
#
# Usage:
#   # Preview – no changes made
#   .\Invoke-SharePointVersionTrim.ps1 -TenantName "contoso" -KeepVersions 10 -WhatIf
#
#   # Keep only 10 versions per file across all sites
#   .\Invoke-SharePointVersionTrim.ps1 -TenantName "contoso" -KeepVersions 10
#
#   # Delete versions older than 180 days, keeping at least 3 per file
#   .\Invoke-SharePointVersionTrim.ps1 -TenantName "contoso" -DeleteOlderThanDays 180 -MinVersionsToKeep 3
#
#   # Target a single site
#   .\Invoke-SharePointVersionTrim.ps1 -TenantName "contoso" -KeepVersions 5 -SiteFilter "https://contoso.sharepoint.com/sites/HR"
# =============================================================================

[CmdletBinding(SupportsShouldProcess, DefaultParameterSetName = "KeepN")]
param (
    [Parameter(Mandatory = $true)]
    [string]$TenantName,

    [Parameter(Mandatory = $false)]
    [string]$AdminUrl,

    # Client ID from Register-SPOVersionManagerApp.ps1 (one-time setup)
    [Parameter(Mandatory = $true)]
    [string]$ClientId,

    # ---- Trim strategy -------------------------------------------------------

    [Parameter(Mandatory = $true, ParameterSetName = "KeepN")]
    [ValidateRange(1, 50000)]
    [int]$KeepVersions,

    [Parameter(Mandatory = $true, ParameterSetName = "OlderThan")]
    [ValidateRange(1, 36500)]
    [int]$DeleteOlderThanDays,

    # When using OlderThan, always keep at least this many recent versions
    [Parameter(Mandatory = $false, ParameterSetName = "OlderThan")]
    [ValidateRange(0, 50000)]
    [int]$MinVersionsToKeep = 1,

    # ---- Scope ---------------------------------------------------------------

    # Optional URL filter – only process sites whose URL contains this string
    [Parameter(Mandatory = $false)]
    [string]$SiteFilter = "",

    # Include OneDrive for Business sites
    [Parameter(Mandatory = $false)]
    [switch]$IncludeOneDrive,

    # Limit total sites for testing (0 = no limit)
    [Parameter(Mandatory = $false)]
    [int]$MaxSites = 0,

    # ---- Output --------------------------------------------------------------

    [Parameter(Mandatory = $false)]
    [string]$OutputPath = ".",

    # Suppress per-file progress output (useful for large tenants)
    [Parameter(Mandatory = $false)]
    [switch]$Quiet
)

#Requires -Modules PnP.PowerShell

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------
function Format-Bytes {
    param ([long]$Bytes)
    switch ($Bytes) {
        { $_ -ge 1GB } { return "{0:N2} GB" -f ($_ / 1GB) }
        { $_ -ge 1MB } { return "{0:N2} MB" -f ($_ / 1MB) }
        { $_ -ge 1KB } { return "{0:N2} KB" -f ($_ / 1KB) }
        default        { return "$_ B" }
    }
}

function Write-Status {
    param ([string]$Message, [string]$Color = "White")
    if (-not $Quiet) { Write-Host "[$(Get-Date -Format 'HH:mm:ss')] $Message" -ForegroundColor $Color }
}

function Connect-SPOSite {
    param ([string]$Url)
    Connect-PnPOnline -Url $Url -ClientId $ClientId -Interactive
}

# ---------------------------------------------------------------------------
# Validate strategy
# ---------------------------------------------------------------------------
if ($PSCmdlet.ParameterSetName -eq "KeepN" -and $KeepVersions -lt 1) {
    throw "-KeepVersions must be >= 1 so the current version is always preserved."
}

$cutoffDate = $null
if ($PSCmdlet.ParameterSetName -eq "OlderThan") {
    $cutoffDate = (Get-Date).AddDays(-$DeleteOlderThanDays)
    Write-Status "Trim strategy: delete versions older than $cutoffDate (keeping >= $MinVersionsToKeep recent)" "Cyan"
} else {
    Write-Status "Trim strategy: keep $KeepVersions most-recent versions per file" "Cyan"
}

if ($WhatIfPreference) {
    Write-Host "`n*** WhatIf mode – NO versions will be deleted ***`n" -ForegroundColor Magenta
}

# ---------------------------------------------------------------------------
# Connect to Admin
# ---------------------------------------------------------------------------
if (-not $AdminUrl) { $AdminUrl = "https://$TenantName-admin.sharepoint.com" }

Write-Status "Connecting to SharePoint Admin: $AdminUrl" "Cyan"
Write-Host "  ClientId : $ClientId" -ForegroundColor DarkGray
Write-Host "  A browser window will open – sign in with your SharePoint Admin account.`n" -ForegroundColor DarkGray
Connect-SPOSite -Url $AdminUrl

# ---------------------------------------------------------------------------
# Retrieve sites
# ---------------------------------------------------------------------------
Write-Status "Retrieving site collections..." "Cyan"

$allSites = Get-PnPTenantSite -IncludeOneDriveSites:$IncludeOneDrive |
    Where-Object { $_.Status -eq "Active" }

if ($SiteFilter) {
    $allSites = $allSites | Where-Object { $_.Url -like "*$SiteFilter*" }
    Write-Status "Filter '$SiteFilter' applied – $($allSites.Count) site(s) match." "Yellow"
}

if ($MaxSites -gt 0) {
    $allSites = $allSites | Select-Object -First $MaxSites
    Write-Status "MaxSites limit applied – processing $($allSites.Count) site(s)." "Yellow"
}

Write-Status "Sites to process: $($allSites.Count)" "Green"

# ---------------------------------------------------------------------------
# Counters and log accumulator
# ---------------------------------------------------------------------------
$grandStats = [PSCustomObject]@{
    SitesProcessed  = 0
    FilesProcessed  = 0
    VersionsDeleted = 0
    BytesReclaimed  = [long]0
    Errors          = 0
}

$trimLog = [System.Collections.Generic.List[PSObject]]::new()

# ---------------------------------------------------------------------------
# Main loop
# ---------------------------------------------------------------------------
$siteIndex = 0
foreach ($site in $allSites) {
    $siteIndex++
    $siteUrl = $site.Url
    Write-Status "`n[$siteIndex/$($allSites.Count)] Site: $siteUrl" "Yellow"

    try {
        Connect-SPOSite -Url $siteUrl

        $lists = Get-PnPList | Where-Object {
            $_.BaseTemplate -eq 101 -and
            -not $_.Hidden
        }

        foreach ($list in $lists) {
            $listDetail = Get-PnPList -Identity $list.Id -Includes EnableVersioning
            if (-not $listDetail.EnableVersioning) {
                Write-Status "  [SKIP] '$($list.Title)' – versioning disabled" "DarkGray"
                continue
            }

            Write-Status "  Library: $($list.Title)" "Gray"

            $camlQuery = "<View Scope='RecursiveAll'><Query><Where><Eq><FieldRef Name='FSObjType'/><Value Type='Integer'>0</Value></Eq></Where></Query><RowLimit>5000</RowLimit></View>"
            $items = Get-PnPListItem -List $list -Query $camlQuery -PageSize 500

            foreach ($item in $items) {
                try {
                    $file = Get-PnPProperty -ClientObject $item -Property File
                    if ($null -eq $file -or $null -eq $file.ServerRelativeUrl) { continue }

                    $versions = Get-PnPProperty -ClientObject $file -Property Versions
                    if ($versions.Count -eq 0) { continue }

                    $orderedVersions = @($versions | Sort-Object -Property Created -Descending)

                    $toDelete = @()
                    if ($PSCmdlet.ParameterSetName -eq "KeepN") {
                        if ($orderedVersions.Count -ge $KeepVersions) {
                            $toDelete = $orderedVersions | Select-Object -Skip $KeepVersions
                        }
                    } else {
                        $candidatesByDate = $orderedVersions | Where-Object { $_.Created -lt $cutoffDate }
                        $safeKeepCount    = [math]::Max($MinVersionsToKeep, 0)
                        if ($orderedVersions.Count -gt $safeKeepCount) {
                            $toDelete = $candidatesByDate | Where-Object {
                                $_ -notin ($orderedVersions | Select-Object -First $safeKeepCount)
                            }
                        }
                    }

                    if ($toDelete.Count -eq 0) { continue }

                    $reclaimBytes = [long]($toDelete | Measure-Object -Property Size -Sum).Sum

                    $grandStats.FilesProcessed++
                    $grandStats.VersionsDeleted += $toDelete.Count
                    $grandStats.BytesReclaimed  += $reclaimBytes

                    $trimLog.Add([PSCustomObject]@{
                        SiteUrl         = $siteUrl
                        Library         = $list.Title
                        FilePath        = $file.ServerRelativeUrl
                        FileName        = $file.Name
                        TotalVersions   = $orderedVersions.Count
                        VersionsDeleted = $toDelete.Count
                        BytesReclaimed  = $reclaimBytes
                        SizeReclaimedHR = Format-Bytes $reclaimBytes
                        WhatIf          = $WhatIfPreference.ToString()
                        Timestamp       = (Get-Date).ToString("o")
                    })

                    if (-not $Quiet) {
                        Write-Host ("    {0} – delete {1} version(s) ({2})" -f
                            $file.Name, $toDelete.Count, (Format-Bytes $reclaimBytes)) `
                            -ForegroundColor $(if ($WhatIfPreference) { "Magenta" } else { "Red" })
                    }

                    foreach ($ver in $toDelete) {
                        if ($PSCmdlet.ShouldProcess(
                            "$siteUrl – $($file.ServerRelativeUrl) v$($ver.VersionLabel)",
                            "Delete version")) {
                            $ver.DeleteObject()
                        }
                    }

                    if (-not $WhatIfPreference) {
                        Invoke-PnPQuery
                    }
                }
                catch {
                    Write-Warning "    Failed on item $($item.Id) in '$($list.Title)': $_"
                    $grandStats.Errors++
                }
            }
        }

        $grandStats.SitesProcessed++
    }
    catch {
        Write-Warning "Failed to process site '$siteUrl': $_"
        $grandStats.Errors++
    }
}

# ---------------------------------------------------------------------------
# Export log
# ---------------------------------------------------------------------------
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$logCsv    = Join-Path $OutputPath "SPO_VersionTrimLog_$timestamp.csv"
$trimLog | Export-Csv -Path $logCsv -NoTypeInformation -Encoding UTF8

# ---------------------------------------------------------------------------
# Console summary
# ---------------------------------------------------------------------------
$modeLabel = if ($WhatIfPreference) { "WHATIF – NO CHANGES MADE" } else { "COMPLETED" }

Write-Host "`n============================================================" -ForegroundColor Cyan
Write-Host " SharePoint Version Trim – $modeLabel" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host " Sites processed    : $($grandStats.SitesProcessed)"
Write-Host " Files processed    : $($grandStats.FilesProcessed)"
Write-Host " Versions deleted   : $($grandStats.VersionsDeleted)"
Write-Host " Storage reclaimed  : $(Format-Bytes $grandStats.BytesReclaimed)"
Write-Host " Errors             : $($grandStats.Errors)"
Write-Host "------------------------------------------------------------"
Write-Host " Log CSV : $logCsv"
Write-Host "============================================================`n" -ForegroundColor Cyan
