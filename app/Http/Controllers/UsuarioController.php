<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\Laravel\Facades\Image;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->paginate(20);
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'prefijo'       => 'nullable|string|max:20',
            'apellidos'     => 'nullable|string|max:100',
            'puesto'        => 'nullable|string|max:100',
            'imagen_firma'  => 'nullable|image|max:2048',
            'email'         => 'required|email|max:255|unique:users',
            'password'      => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name'          => $validated['name'],
            'prefijo'       => $validated['prefijo'] ?? null,
            'apellidos'     => $validated['apellidos'] ?? null,
            'puesto'        => $validated['puesto'] ?? null,
            'imagen_firma'  => $request->hasFile('imagen_firma')
                ? $this->procesarImagenFirma($request->file('imagen_firma'))
                : null,
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'prefijo'       => 'nullable|string|max:20',
            'apellidos'     => 'nullable|string|max:100',
            'puesto'        => 'nullable|string|max:100',
            'imagen_firma'  => 'nullable|image|max:2048',
            'email'         => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'password'      => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if ($request->hasFile('imagen_firma')) {
            if ($usuario->imagen_firma) {
                Storage::disk('public')->delete($usuario->imagen_firma);
            }
            $validated['imagen_firma'] = $this->procesarImagenFirma($request->file('imagen_firma'));
        }

        $usuario->update([
            'name'         => $validated['name'],
            'prefijo'      => $validated['prefijo'] ?? null,
            'apellidos'    => $validated['apellidos'] ?? null,
            'puesto'       => $validated['puesto'] ?? null,
            'email'        => $validated['email'],
            ...(array_key_exists('imagen_firma', $validated)
                ? ['imagen_firma' => $validated['imagen_firma']]
                : []),
            ...($request->filled('password')
                ? ['password' => Hash::make($request->password)]
                : []),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->imagen_firma) {
            Storage::disk('public')->delete($usuario->imagen_firma);
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

        private function procesarImagenFirma($file): string
    {
        $imagen = Image::decode($file)
            ->scaleDown(width: 800, height: 400);

        $nombreArchivo = 'firmas/' . Str::uuid() . '.png';

        Storage::disk('public')->put(
            $nombreArchivo,
            $imagen->encodeUsingFileExtension('png')
        );

        return $nombreArchivo;
    }
}