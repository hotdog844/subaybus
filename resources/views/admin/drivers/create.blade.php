<form action="{{ route('admin.drivers.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div style="margin-bottom: 1rem;">
        <label for="name" style="display: block; margin-bottom: 0.5rem;">Driver's Full Name</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('name') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    {{-- New Email Field --}}
    <div style="margin-bottom: 1rem;">
        <label for="email" style="display: block; margin-bottom: 0.5rem;">Email (for login)</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('email') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="license_number" style="display: block; margin-bottom: 0.5rem;">License Number</label>
        <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('license_number') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    <div style="margin-bottom: 1rem;">
        <label for="contact_number" style="display: block; margin-bottom: 0.5rem;">Contact Number (Optional)</label>
        <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('contact_number') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    <div style="margin-bottom: 1rem;">
    <label for="license_image" style="display: block; margin-bottom: 0.5rem;">Upload Driver's License</label>
    <input type="file" id="license_image" name="license_image" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
    @error('license_image') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
</div>

<div style="margin-bottom: 1rem;">
    <label for="cert_image" style="display: block; margin-bottom: 0.5rem;">Upload Medical Certificate</label>
    <input type="file" id="cert_image" name="cert_image" required style="width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px;">
    @error('cert_image') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
</div>

    {{-- New Password Field --}}
    <div style="margin-bottom: 1rem;">
        <label for="password" style="display: block; margin-bottom: 0.5rem;">Password</label>
        <input type="password" id="password" name="password" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
        @error('password') <p style="color: red; font-size: 0.875rem;">{{ $message }}</p> @enderror
    </div>

    <button type="submit" style="background: #27ae60; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;">Save Driver</button>
</form>