Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\Users\Admin\Desktop\images\login page.jpeg")

# Sample button color (center of blue Login button ~ x=880, y=430)
$btnColor = $bmp.GetPixel(880, 430)
Write-Host "Login Button RGB: $($btnColor.R), $($btnColor.G), $($btnColor.B) -> Hex: #$($btnColor.R.ToString('X2'))$($btnColor.G.ToString('X2'))$($btnColor.B.ToString('X2'))"

# Sample "Welcome Back" text color (~ x=880, y=145)
$titleColor = $bmp.GetPixel(880, 145)
Write-Host "Welcome Back Title RGB: $($titleColor.R), $($titleColor.G), $($titleColor.B)"

# Sample "Your Home" title color (~ x=150, y=185)
$heroTitleColor = $bmp.GetPixel(150, 185)
Write-Host "Hero Title RGB: $($heroTitleColor.R), $($heroTitleColor.G), $($heroTitleColor.B)"

# Sample background wave color on right (~ x=1250, y=50)
$waveTopColor = $bmp.GetPixel(1250, 50)
Write-Host "Top-Right Wave RGB: $($waveTopColor.R), $($waveTopColor.G), $($waveTopColor.B)"

# Sample house outline blue in logo (~ x=70, y=85)
for ($x = 55; $x -lt 90; $x++) {
    for ($y = 70; $y -lt 105; $y++) {
        $c = $bmp.GetPixel($x, $y)
        if ($c.B -gt 180 -and $c.R -lt 50) {
            Write-Host "Logo Blue RGB: $($c.R), $($c.G), $($c.B) -> Hex: #$($c.R.ToString('X2'))$($c.G.ToString('X2'))$($c.B.ToString('X2'))"
            break
        }
    }
}

$bmp.Dispose()
