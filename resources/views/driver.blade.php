<!DOCTYPE html>
<html>
<head>
    <title>Driver Test Page</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Send Location Update</h1>

    <form id="locationForm">
        <label>Plate Number:</label><br>
        <input type="text" name="plate_number" value="RXS-001"><br><br>

        <label>Latitude:</label><br>
        <input type="text" name="latitude" value="11.597812"><br><br>

        <label>Longitude:</label><br>
        <input type="text" name="longitude" value="122.753049"><br><br>

        <button type="submit">Send Location</button>
    </form>

    <p id="status" style="color: green;"></p>

    <script>
        document.getElementById('locationForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('/update-location', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('status').innerText = data.message;
            })
            .catch(error => {
                document.getElementById('status').innerText = "Error: " + error.message;
            });
        });
    </script>
</body>
</html>
