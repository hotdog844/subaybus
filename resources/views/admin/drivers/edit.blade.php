<form action="{{ route('admin.drivers.update', $driver) }}" method="POST">
    @csrf
    @method('PUT')

    <div style="margin-bottom: 1rem;">
        <label for="name" style="display: block; margin-bottom: 0.5rem;">Driver's Full Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $driver->name) }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('name') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    {{-- Email Field --}}
    <div style="margin-bottom: 1rem;">
        <label for="email" style="display: block; margin-bottom: 0.5rem;">Email (for login)</label>
        <input type="email" id="email" name="email" value="{{ old('email', $driver->email) }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('email') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="license_number" style="display: block; margin-bottom: 0.5rem;">License Number</label>
        <input type="text" id="license_number" name="license_number" value="{{ old('license_number', $driver->license_number) }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('license_number') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="contact_number" style="display: block; margin-bottom: 0.5rem;">Contact Number (Optional)</label>
        <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $driver->contact_number) }}" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('contact_number') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    <hr style="border: 1px solid #eee; margin: 2rem 0;">

    {{-- Optional Password Fields --}}
    <div style="margin-bottom: 1rem;">
        <label for="password" style="display: block; margin-bottom: 0.5rem;">New Password (Leave blank to keep current password)</label>
        <input type="password" id="password" name="password" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('password') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="password_confirmation" style="display: block; margin-bottom: 0.5rem;">Confirm New Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
    </div>

    <button type="submit" style="background: #007bff; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Update Driver</button>
</form>