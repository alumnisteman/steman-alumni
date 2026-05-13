$files = Get-ChildItem -Path "resources\views\admin" -Recurse -Filter "*.blade.php"
foreach ($f in $files) {
    $content = Get-Content $f.FullName -Raw
    $changed = $false
    
    if ($content.Contains("@extends('layouts.admin')")) {
        $content = $content.Replace("@extends('layouts.admin')", "@extends('layouts.app')")
        $changed = $true
    }
    if ($content.Contains("@section('admin-content')")) {
        $content = $content.Replace("@section('admin-content')", "@section('content')")
        $changed = $true
    }
    
    if ($changed) {
        Set-Content $f.FullName -Value $content -NoNewline
        Write-Host "Reverted: $($f.Name)"
    }
}
