<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - SubayBus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        :root {
            --primary-color: #00D084;
            --header-bg: #0A5C36;
            --text-dark: #222;
            --text-light: #6c757d;
            --page-bg: #f4f7fa;
            --card-bg: #ffffff;
            --border-color: #e9ecef;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--page-bg); }
        .header { background-color: var(--header-bg); color: white; padding: 1.2rem; text-align: center; display: flex; align-items: center; }
        .header .back-arrow { font-size: 1.5rem; color: white; text-decoration: none; margin-right: 1rem; }
        .header h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
        .content-padding { padding: 2rem 1rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-dark); }
        input { width: 100%; padding: 0.9rem 1.2rem; border: 1px solid var(--border-color); border-radius: 50px; font-size: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .btn-submit { width: 100%; padding: 1rem; background-color: var(--primary-color); color: white; border: none; border-radius: 50px; font-size: 1.1rem; font-weight: 500; cursor: pointer; box-shadow: 0 4px 15px rgba(0, 208, 132, 0.4); margin-top: 1rem; }
        .profile-pic-group { display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; background: var(--card-bg); padding: 1rem; border-radius: 16px; }
        .profile-pic-preview { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>
    <header class="header">
        <a href="{{ route('profile.show') }}" class="back-arrow">←</a>
        <h1>Edit Profile</h1>
    </header>

    <div class="content-padding">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="form-group profile-pic-group">
                <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=128&color=0A5C36&background=EBF4FF' }}" alt="Profile Picture" class="profile-pic-preview">
                <div>
                    <label for="profile_photo">Change Profile Photo</label>
                    <input id="profile_photo" type="file" name="profile_photo">
                </div>
            </div>

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            
             <div class="form-group">
                <label for="phone">Phone Number</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" required>
            </div>

            <button type="submit" class="btn-submit">Save Changes</button>
        </form>
    </div>
</body>
</html>