<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f8;
    }
    .mobile-navbar {
      background-color: #007bff;
      color: #fff;
      padding: 1rem;
      font-size: 1.2rem;
      text-align: center;
      font-weight: bold;
    }
    .form-container {
      padding: 1.5rem;
      max-width: 600px;
      margin: 0 auto;
    }
    label {
      font-weight: bold;
      display: block;
      margin: 1rem 0 0.3rem;
    }
    input, select {
      width: 100%;
      padding: 0.7rem;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    button {
      margin-top: 1.5rem;
      padding: 0.8rem;
      width: 100%;
      background-color: #007bff;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="mobile-navbar">✏️ Edit Profile</div>

<div class="form-container">
  <form action="{{ route('mobile.updateProfile') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Name</label>
    <input type="text" name="name" value="{{ auth()->user()->name }}" required>

    <label>Email</label>
    <input type="email" name="email" value="{{ auth()->user()->email }}" required>

    <label>Phone</label>
    <input type="text" name="phone" value="{{ auth()->user()->phone }}" required>

    <label>Passenger Type</label>
    <select name="passenger_type" required>
      <option value="regular" {{ auth()->user()->passenger_type == 'regular' ? 'selected' : '' }}>Regular</option>
      <option value="student" {{ auth()->user()->passenger_type == 'student' ? 'selected' : '' }}>Student</option>
      <option value="senior"  {{ auth()->user()->passenger_type == 'senior' ? 'selected' : '' }}>Senior</option>
      <option value="pwd"     {{ auth()->user()->passenger_type == 'pwd' ? 'selected' : '' }}>PWD</option>
    </select>

    <label>ID Card (Optional)</label>
    <input type="file" name="id_card">

    <button type="submit">Update Profile</button>
  </form>
</div>

</body>
</html>
