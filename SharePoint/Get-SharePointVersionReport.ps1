# =============================================================================
# Get-SharePointVersionReport.ps1
# Description : Analyses all SharePoint Online sites and reports on files,
#               version counts, and total size consumed by versions.
# Requirements: PnP.PowerShell module
#               Install-Module PnP.PowerShell -Force
# Usage       : .\Get-SharePointVersionReport.ps1 -TenantName "contoso"
#               [-OutputPath "C:\Reports"] [-AdminUrl "https://tenant-admin.sharepoint.com"]
# =============================================================================

[CmdletBinding()]
param (
    [Parameter(Mandatory = $true)]
    [string]$TenantName,

    [Parameter(Mandatory = $false)]
    [string]$AdminUrl,

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
# Connect to the SharePoint Admin centre
# ---------------------------------------------------------------------------
if (-not $AdminUrl) {
    $AdminUrl = "https://$TenantName-admin.sharepoint.com"
}

Write-Host "`n[$(Get-Date -Format 'HH:mm:ss')] Connecting to SharePoint Admin: $AdminUrl" -ForegroundColor Cyan
Connect-PnPOnline -Url $AdminUrl -Interactive

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
    TotalFiles       = 0
    TotalVersions    = 0
    TotalVersionBytes= [long]0
}

$siteIndex = 0
foreach ($site in $allSites) {
    $siteIndex++
    $siteUrl = $site.Url
    Write-Host "`n[$siteIndex/$($allSites.Count)] Processing: $siteUrl" -ForegroundColor Yellow

    try {
        Connect-PnPOnline -Url $siteUrl -Interactive

        $lists = Get-PnPList | Where-Object {
            $_.BaseTemplate -eq 101 -and   # Document Library
            -not $_.Hidden
        }

        $siteFileCount    = 0
        $siteVersionCount = 0
        $siteVersionBytes = [long]0

        foreach ($list in $lists) {
            Write-Host "  Library: $($list.Title)" -ForegroundColor Gray

            # Get all files (ListItems with FileSystemObjectType = File)
            $camlQuery = "<View Scope='RecursiveAll'><Query><Where><Eq><FieldRef Name='FSObjType'/><Value Type='Integer'>0</Value></Eq></Where></Query><RowLimit>5000</RowLimit></View>"

            $items = Get-PnPListItem -List $list -Query $camlQuery -PageSize 500

            foreach ($item in $items) {
                try {
                    $file = Get-PnPProperty -ClientObject $item -Property File
                    if ($null -eq $file -or $file.ServerRelativeUrl -eq $null) { continue }

                    # Retrieve version history
                    $versions = Get-PnPProperty -ClientObject $file -Property Versions
                    $versionCount = $versions.Count

                    if ($versionCount -lt $MinVersionCount) { continue }

                    # Calculate total bytes used by all versions
                    $versionBytes = [long]0
                    foreach ($ver in $versions) {
                        $versionBytes += [long]$ver.Size
                    }

                    $versionMB = [math]::Round($versionBytes / 1MB, 4)
                    if ($versionMB -lt $MinVersionSizeMB) { continue }

                    $siteFileCount++
                    $siteVersionCount += $versionCount
                    $siteVersionBytes += $versionBytes

                    $reportRow = [PSCustomObject]@{
                        SiteUrl         = $siteUrl
                        Library         = $list.Title
                        FilePath        = $file.ServerRelativeUrl
                        FileName        = $file.Name
                        CurrentVersion  = $file.UIVersionLabel
                        VersionCount    = $versionCount
                        VersionSizeBytes= $versionBytes
                        VersionSizeMB   = $versionMB
                        VersionSizeHR   = Format-Bytes $versionBytes
                        LastModified    = $item["Modified"]
                        ModifiedBy      = $item["Editor"].LookupValue
                    }
                    $report.Add($reportRow)
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
            SiteUrl          = $siteUrl
            LibraryCount     = $lists.Count
            FileCount        = $siteFileCount
            TotalVersions    = $siteVersionCount
            TotalVersionBytes= $siteVersionBytes
            TotalVersionSizeMB = [math]::Round($siteVersionBytes / 1MB, 2)
            TotalVersionSizeHR = Format-Bytes $siteVersionBytes
        })

        Write-Host "  -> Files: $siteFileCount  |  Versions: $siteVersionCount  |  Version Storage: $(Format-Bytes $siteVersionBytes)" -ForegroundColor Green
    }
    catch {
        Write-Warning "Failed to process site '$siteUrl': $_"
        $siteSummary.Add([PSCustomObject]@{
            SiteUrl          = $siteUrl
            LibraryCount     = 0
            FileCount        = 0
            TotalVersions    = 0
            TotalVersionBytes= 0
            TotalVersionSizeMB = 0
            TotalVersionSizeHR = "ERROR"
        })
    }
}

# ---------------------------------------------------------------------------
# Export results
# ---------------------------------------------------------------------------
$timestamp   = Get-Date -Format "yyyyMMdd_HHmmss"
$detailCsv   = Join-Path $OutputPath "SPO_VersionDetail_$timestamp.csv"
$summaryCsv  = Join-Path $OutputPath "SPO_VersionSummary_$timestamp.csv"

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
