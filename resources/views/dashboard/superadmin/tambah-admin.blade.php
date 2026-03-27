<h2>Tambah Admin</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form action="{{ route('superadmin.tambah-admin') }}" method="POST">
    @csrf

    <div class="form-group">
        <label>Nama</label>
        <input type="text" name="name" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>

    <div class="form-group">
        <label>No HP</label>
        <input type="text" name="phone_number" required>
    </div>

    <input type="hidden" name="role" value="admin">

    <button type="submit">Tambah Admin</button>
</form>