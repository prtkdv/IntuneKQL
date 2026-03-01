# =============================================================================
# Get-SharePointVersionReport.ps1
# Description : Analyses all SharePoint Online sites and reports on files,
#               version counts, and total size consumed by versions.
# Requirements: PnP.PowerShell module  (v2+)
#               Install-Module PnP.PowerShell -Force
#
# Authentication (choose one):
#   Option A – PnP Management Shell app (no app registration needed, one-time
#              admin consent required):
#               Register-PnPManagementShellAccess   # run once as Global Admin
#              Then run this script without -ClientId (uses the default below).
#
#   Option B – Your own Entra ID App Registration:
#               Pass -ClientId "<your-app-client-id>"
#               The app needs Sites.FullControl.All (application) + admin consent,
#               or Sites.Read.All delegated + SharePoint admin rights for the user.
#
# Usage:
#   .\Get-SharePointVersionReport.ps1 -TenantName "contoso"
#   .\Get-SharePointVersionReport.ps1 -TenantName "contoso" -ClientId "<guid>" -OutputPath "C:\Reports"
# =============================================================================

[CmdletBinding()]
param (
    [Parameter(Mandatory = $true)]
    [string]$TenantName,

    [Parameter(Mandatory = $false)]
    [string]$AdminUrl,

    # PnP Management Shell multi-tenant app (public, no custom app needed).
    # Override with your own app registration Client ID if preferred.
    [Parameter(Mandatory = $false)]
    [string]$ClientId = "31359c7f-bd7e-475c-86db-fdb8c937548e",

    [Parameter(Mandatory = $false)]
    [string]$OutputPath = ".",

    # Limit results for testing (0 = no limit)
    [Parameter(Mandatory = $false)]
    [int]$MaxSites = 0,

    # Only report files that have more than this many versions
    [Parameter(Mandatory = $false)]
    [int]$MinVersionCount = 1,

    # Only report files whose version storage exceeds this many MB
    [Parameter(Mandatory = $false)]
    [double]$MinVersionSizeMB = 0
)

#Requires -Modules PnP.PowerShell

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

# ---------------------------------------------------------------------------
# Helper – convert bytes to a human-readable string
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

# ---------------------------------------------------------------------------
# Helper – connect to a SharePoint URL with interactive browser login
# ---------------------------------------------------------------------------
function Connect-SPOSite {
    param ([string]$Url)
    Connect-PnPOnline -Url $Url -ClientId $ClientId -Interactive
}

# ---------------------------------------------------------------------------
# Connect to the SharePoint Admin centre
# ---------------------------------------------------------------------------
if (-not $AdminUrl) {
    $AdminUrl = "https://$TenantName-admin.sharepoint.com"
}

Write-Host "`n[$(Get-Date -Format 'HH:mm:ss')] Connecting to SharePoint Admin: $AdminUrl" -ForegroundColor Cyan
Write-Host "  ClientId: $ClientId" -ForegroundColor DarkGray
Write-Host "  (If this is your first run, a browser window will open for sign-in.)`n" -ForegroundColor DarkGray
Connect-SPOSite -Url $AdminUrl

# ---------------------------------------------------------------------------
# Retrieve all site collections
# ---------------------------------------------------------------------------
Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Retrieving site collections..." -ForegroundColor Cyan

$allSites = Get-PnPTenantSite -IncludeOneDriveSites:$false |
    Where-Object { $_.Status -eq "Active" }

if ($MaxSites -gt 0) {
    $allSites = $allSites | Select-Object -First $MaxSites
}

Write-Host "[$(Get-Date -Format 'HH:mm:ss')] Found $($allSites.Count) active site(s)." -ForegroundColor Green

# ---------------------------------------------------------------------------
# Iterate sites and collect version data
# ---------------------------------------------------------------------------
$report        = [System.Collections.Generic.List[PSObject]]::new()
$siteSummary   = [System.Collections.Generic.List[PSObject]]::new()
$grandTotal    = [PSCustomObject]@{
    TotalFiles        = 0
    TotalVersions     = 0
    TotalVersionBytes = [long]0
}

$siteIndex = 0
foreach ($site in $allSites) {
    $siteIndex++
    $siteUrl = $site.Url
    Write-Host "`n[$siteIndex/$($allSites.Count)] Processing: $siteUrl" -ForegroundColor Yellow

    try {
        Connect-SPOSite -Url $siteUrl

        $lists = Get-PnPList | Where-Object {
            $_.BaseTemplate -eq 101 -and   # Document Library
            -not $_.Hidden
        }

        $siteFileCount    = 0
        $siteVersionCount = 0
        $siteVersionBytes = [long]0

        foreach ($list in $lists) {
            Write-Host "  Library: $($list.Title)" -ForegroundColor Gray

            $camlQuery = "<View Scope='RecursiveAll'><Query><Where><Eq><FieldRef Name='FSObjType'/><Value Type='Integer'>0</Value></Eq></Where></Query><RowLimit>5000</RowLimit></View>"
            $items = Get-PnPListItem -List $list -Query $camlQuery -PageSize 500

            foreach ($item in $items) {
                try {
                    $file = Get-PnPProperty -ClientObject $item -Property File
                    if ($null -eq $file -or $null -eq $file.ServerRelativeUrl) { continue }

                    $versions     = Get-PnPProperty -ClientObject $file -Property Versions
                    $versionCount = $versions.Count

                    if ($versionCount -lt $MinVersionCount) { continue }

                    $versionBytes = [long]0
                    foreach ($ver in $versions) { $versionBytes += [long]$ver.Size }

                    $versionMB = [math]::Round($versionBytes / 1MB, 4)
                    if ($versionMB -lt $MinVersionSizeMB) { continue }

                    $siteFileCount++
                    $siteVersionCount += $versionCount
                    $siteVersionBytes += $versionBytes

                    $report.Add([PSCustomObject]@{
                        SiteUrl          = $siteUrl
                        Library          = $list.Title
                        FilePath         = $file.ServerRelativeUrl
                        FileName         = $file.Name
                        CurrentVersion   = $file.UIVersionLabel
                        VersionCount     = $versionCount
                        VersionSizeBytes = $versionBytes
                        VersionSizeMB    = $versionMB
                        VersionSizeHR    = Format-Bytes $versionBytes
                        LastModified     = $item["Modified"]
                        ModifiedBy       = $item["Editor"].LookupValue
                    })
                }
                catch {
                    Write-Warning "    Could not process item '$($item.Id)' in '$($list.Title)': $_"
                }
            }
        }

        $grandTotal.TotalFiles        += $siteFileCount
        $grandTotal.TotalVersions     += $siteVersionCount
        $grandTotal.TotalVersionBytes += $siteVersionBytes

        $siteSummary.Add([PSCustomObject]@{
            SiteUrl             = $siteUrl
            LibraryCount        = $lists.Count
            FileCount           = $siteFileCount
            TotalVersions       = $siteVersionCount
            TotalVersionBytes   = $siteVersionBytes
            TotalVersionSizeMB  = [math]::Round($siteVersionBytes / 1MB, 2)
            TotalVersionSizeHR  = Format-Bytes $siteVersionBytes
        })

        Write-Host "  -> Files: $siteFileCount  |  Versions: $siteVersionCount  |  Version Storage: $(Format-Bytes $siteVersionBytes)" -ForegroundColor Green
    }
    catch {
        Write-Warning "Failed to process site '$siteUrl': $_"
        $siteSummary.Add([PSCustomObject]@{
            SiteUrl            = $siteUrl
            LibraryCount       = 0
            FileCount          = 0
            TotalVersions      = 0
            TotalVersionBytes  = 0
            TotalVersionSizeMB = 0
            TotalVersionSizeHR = "ERROR"
        })
    }
}

# ---------------------------------------------------------------------------
# Export results
# ---------------------------------------------------------------------------
$timestamp  = Get-Date -Format "yyyyMMdd_HHmmss"
$detailCsv  = Join-Path $OutputPath "SPO_VersionDetail_$timestamp.csv"
$summaryCsv = Join-Path $OutputPath "SPO_VersionSummary_$timestamp.csv"

$report      | Export-Csv -Path $detailCsv  -NoTypeInformation -Encoding UTF8
$siteSummary | Export-Csv -Path $summaryCsv -NoTypeInformation -Encoding UTF8

# ---------------------------------------------------------------------------
# Console summary
# ---------------------------------------------------------------------------
Write-Host "`n============================================================" -ForegroundColor Cyan
Write-Host " SharePoint Version Report – Grand Total" -ForegroundColor Cyan
Write-Host "============================================================" -ForegroundColor Cyan
Write-Host " Sites processed   : $siteIndex"
Write-Host " Total files       : $($grandTotal.TotalFiles)"
Write-Host " Total versions    : $($grandTotal.TotalVersions)"
Write-Host " Total version size: $(Format-Bytes $grandTotal.TotalVersionBytes)"
Write-Host "------------------------------------------------------------"
Write-Host " Detail CSV  : $detailCsv"
Write-Host " Summary CSV : $summaryCsv"
Write-Host "============================================================`n" -ForegroundColor Cyan
