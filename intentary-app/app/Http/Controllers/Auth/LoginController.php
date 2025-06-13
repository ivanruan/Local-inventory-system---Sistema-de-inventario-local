<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException; // Importa esta clase

class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        // Define aquí el campo que Laravel debe usar como "username" para la autenticación.
        // Como tu formulario usa 'nombre' y tu modelo lo usa, debe ser 'nombre'.
        return 'nombre';
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // 1. Validar los datos de la solicitud
        $request->validate([
            $this->username() => 'required|string', // Usa $this->username() para coherencia
            'password' => 'required|string',
        ], [
            $this->username() . '.required' => 'El campo nombre de usuario es obligatorio.',
            'password.required' => 'El campo contraseña es obligatorio.',
        ]);

        // 2. Preparar las credenciales
        $credentials = $request->only($this->username(), 'password');
        $credentials['activo'] = 1; // Asegúrate de que el usuario esté activo

        // 3. Intentar autenticar al usuario
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Si la autenticación es exitosa
            $request->session()->regenerate(); // Regenerar la sesión para prevenir fijación de sesión

            return redirect()->intended($this->redirectPath()); // Redirigir a la URL intencionada o al dashboard
        }

        // 4. Si la autenticación falla, lanzar una excepción de validación.
        // Laravel capturará esto y redirigirá de vuelta con los errores y los old inputs.
        throw ValidationException::withMessages([
            $this->username() => [trans('Autenticacion fallida')], // Usa trans('auth.failed') para el mensaje estándar de Laravel
        ])->redirectTo(route('login')); // Redirigir explícitamente a la ruta de login para mayor seguridad.
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Puedes redirigir a la página de inicio o a la de login con un mensaje de éxito.
        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Get the redirect path after login.
     *
     * @return string
     */
    protected function redirectPath()
    {
        return $this->redirectTo;
    }
}