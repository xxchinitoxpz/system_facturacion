<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-8">
        <div class="bg-white rounded-2xl shadow p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.485 0 4.797.657 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Perfil de usuario
            </h2>
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="bg-white rounded-2xl shadow p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
                Cambiar contraseña
            </h2>
            @include('profile.partials.update-password-form')
        </div>
        <div class="bg-white rounded-2xl shadow p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Eliminar cuenta
            </h2>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
