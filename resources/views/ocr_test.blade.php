<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SubayBus OCR Test</title>
    <style>
        body { font-family: sans-serif; padding: 50px; background: #f4f4f4; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; font-size: 16px; border-radius: 4px; }
        button:hover { background: #218838; }
        #result { margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 4px; white-space: pre-wrap; display: none; }
        .success { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>Student ID Verification</h2>
    <p>Upload an ID and type the name to test the AI.</p>

    <form id="ocrForm">
        <label>Student Name (as shown on ID):</label>
        <input type="text" id="student_name" placeholder="Ex: JUAN DELA CRUZ" required>

        <label>Upload ID Photo:</label>
        <input type="file" id="id_image" accept="image/*" required>

        <button type="submit" id="btnSubmit">Verify ID</button>
    </form>

    <div id="result"></div>
</div>

<script>
    document.getElementById('ocrForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnSubmit');
        const resultDiv = document.getElementById('result');
        const name = document.getElementById('student_name').value;
        const file = document.getElementById('id_image').files[0];

        // Prepare Data
        let formData = new FormData();
        formData.append('student_name', name);
        formData.append('id_image', file);

        // UI Updates
        btn.textContent = "Scanning... (Please Wait)";
        btn.disabled = true;
        resultDiv.style.display = 'block';
        resultDiv.textContent = "Uploading and Scanning...";

        try {
            // Send to Laravel API
            const response = await fetch('/api/verify-id', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            // Show Result
            if (data.status === 'success') {
                resultDiv.innerHTML = `<span class="success">✅ ${data.message}</span><br><br><small><strong>Raw Text Found:</strong><br>${data.detected_text}</small>`;
            } else {
                resultDiv.innerHTML = `<span class="fail">❌ ${data.message}</span><br><br><small><strong>Raw Text Found:</strong><br>${data.debug_text_found || data.message}</small>`;
            }

        } catch (error) {
            resultDiv.innerHTML = `<span class="fail">⚠️ System Error: ${error.message}</span>`;
        }

        btn.textContent = "Verify ID";
        btn.disabled = false;
    });
</script>

</body>
</html>