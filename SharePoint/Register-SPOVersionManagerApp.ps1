# =============================================================================
# Register-SPOVersionManagerApp.ps1
# Description : One-time setup script. Creates an Entra ID App Registration
#               with the permissions required by the SharePoint Version Report
#               and Version Trim scripts.
#
# Requirements: PnP.PowerShell v2+  (Install-Module PnP.PowerShell -Force)
#               You must be a Global Administrator or Application Administrator
#               to register apps and grant admin consent.
#
# Usage:
#   .\Register-SPOVersionManagerApp.ps1 -TenantName "contoso"
#
# Output:
#   Prints the Client ID to the console and saves it to ClientId.txt in the
#   same directory. Pass that Client ID to the main scripts via -ClientId.
# =============================================================================

[CmdletBinding()]
param (
    [Parameter(Mandatory = $true)]
    [string]$TenantName,

    # Friendly name shown in Entra ID > App registrations
    [Parameter(Mandatory = $false)]
    [string]$AppName = "SPO Version Manager"
)

#Requires -Modules PnP.PowerShell

$tenant = "$TenantName.onmicrosoft.com"

Write-Host "`n[1/2] Registering Entra ID app '$AppName' in tenant '$tenant'..." -ForegroundColor Cyan
Write-Host "      A browser window will open – sign in as a Global Administrator.`n" -ForegroundColor DarkGray

# Register-PnPEntraIDAppForInteractiveLogin creates the app, configures
# delegated SharePoint permissions, and opens a browser for admin consent.
$app = Register-PnPEntraIDAppForInteractiveLogin `
    -ApplicationName $AppName `
    -Tenant $tenant `
    -SharePoint `
    -Interactive

if (-not $app) {
    Write-Error "App registration failed. Ensure you are a Global Administrator and try again."
    exit 1
}

$clientId = $app."AzureAppId/ClientId"

Write-Host "`n[2/2] App registered successfully." -ForegroundColor Green
Write-Host "------------------------------------------------------------"
Write-Host " App Name  : $AppName"
Write-Host " Client ID : $clientId"
Write-Host "------------------------------------------------------------"

# Save to file for convenience
$outFile = Join-Path $PSScriptRoot "ClientId.txt"
$clientId | Out-File -FilePath $outFile -Encoding UTF8 -Force
Write-Host " Saved to  : $outFile"

Write-Host @"

Next steps
----------
Use the Client ID above when running the main scripts:

  .\Get-SharePointVersionReport.ps1 -TenantName "$TenantName" -ClientId "$clientId"

  .\Invoke-SharePointVersionTrim.ps1 -TenantName "$TenantName" -ClientId "$clientId" -KeepVersions 10 -WhatIf

"@ -ForegroundColor Cyan
