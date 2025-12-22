<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Peta Kustom</title>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.0.0/mapbox-gl.css" rel="stylesheet">
    <style>#map { width: 100%; height: 100vh; }</style>
</head>
<body>
<div id="map"></div>

<script src="https://api.mapbox.com/mapbox-gl-js/v3.0.0/mapbox-gl.js"></script>
<script>
    mapboxgl.accessToken = '{{ env("MAPBOX_ACCESS_TOKEN") }}';
    const map = new mapboxgl.Map({
        container: 'map',
        style: '{{ env("MAPBOX_STYLE_URL") }}', // Style kustom kamu
        center: [98.86666532178721, 3.5500580530962775],
        zoom: 18,
		bearing: 180,
		pitch: 45
		
    });
</script>
</body>
</html>