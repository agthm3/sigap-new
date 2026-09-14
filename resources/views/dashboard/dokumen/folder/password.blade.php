<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Folder Terkunci — SIGAP BRIDA</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
    <div class="text-center mb-6">
      <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mb-3">
        🔒
      </div>
      <h1 class="text-lg font-bold text-gray-900">Folder Dilindungi Sandi</h1>
      <p class="text-xs text-gray-500 mt-1">Folder <strong>{{ $folder->name }}</strong> membutuhkan sandi akses.</p>
    </div>

    @if($errors->any())
      <div class="mb-4 p-3 bg-red-50 text-red-700 text-xs rounded-lg font-semibold">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('sigap-dokumen.shared.unlock', $token) }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block text-xs font-bold text-gray-700 mb-1">Kata Sandi</label>
        <input type="password" name="password" required autofocus class="w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:ring-2 focus:ring-red-900 focus:outline-none">
      </div>
      <button type="submit" class="w-full py-2.5 rounded-lg bg-red-900 text-white font-bold text-sm hover:bg-red-800 transition">
        Buka Folder
      </button>
    </form>
  </div>
</body>
</html>