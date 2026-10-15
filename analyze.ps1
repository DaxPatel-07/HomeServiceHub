Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\Users\Admin\Desktop\images\login page.jpeg")

$bg = $bmp.GetPixel(10, 10)
Write-Host "Top-left BG color: $($bg.R), $($bg.G), $($bg.B)"

$minX = 1280; $maxX = 0; $minY = 714; $maxY = 0
for ($y = 280; $y -lt 714; $y += 2) {
    for ($x = 0; $x -lt 580; $x += 2) {
        $c = $bmp.GetPixel($x, $y)
        $diff = [Math]::Abs($c.R - $bg.R) + [Math]::Abs($c.G - $bg.G) + [Math]::Abs($c.B - $bg.B)
        if ($diff -gt 15) {
            if ($x -lt $minX) { $minX = $x }
            if ($x -gt $maxX) { $maxX = $x }
            if ($y -lt $minY) { $minY = $y }
            if ($y -gt $maxY) { $maxY = $y }
        }
    }
}
Write-Host "Illustration Bounding Box: minX=$minX, maxX=$maxX, minY=$minY, maxY=$maxY"

$cardMinX = 1280; $cardMaxX = 0; $cardMinY = 714; $cardMaxY = 0
for ($y = 40; $y -lt 680; $y += 2) {
    for ($x = 600; $x -lt 1200; $x += 2) {
        $c = $bmp.GetPixel($x, $y)
        if ($c.R -gt 250 -and $c.G -gt 250 -and $c.B -gt 250) {
            if ($x -lt $cardMinX) { $cardMinX = $x }
            if ($x -gt $cardMaxX) { $cardMaxX = $x }
            if ($y -lt $cardMinY) { $cardMinY = $y }
            if ($y -gt $cardMaxY) { $cardMaxY = $y }
        }
    }
}
Write-Host "Card Bounding Box: minX=$cardMinX, maxX=$cardMaxX, minY=$cardMinY, maxY=$cardMaxY"

# Also check title position:
for ($y = 80; $y -lt 280; $y += 5) {
    for ($x = 40; $x -lt 500; $x += 5) {
        $c = $bmp.GetPixel($x, $y)
        if ($c.R -lt 70 -and $c.G -lt 50 -and $c.B -lt 40) {
            Write-Host "Title dark pixel at x=$x, y=$y, color=$($c.R),$($c.G),$($c.B)"
            break
        }
    }
}

$bmp.Dispose()
