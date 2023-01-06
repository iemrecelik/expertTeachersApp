<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            $message = $e->getMessage();
            $permissions = $e->getRequiredPermissions();

            if("User does not have the right permissions." === $e->getMessage()) {
                $message = 'BU İŞLEMİ YAPMAYA YETKİNİZ YOKTUR.';
            }
            
            if("User does not have the right roles." === $e->getMessage()) {
                $message = 'BU İŞLEMİ YAPMAYA YETKİNİZ YOKTUR.';
            }


            if (config('permission.display_permission_in_exception')) {
                $message .= ' Necessary permissions are '.implode(', ', $permissions);
            }

            abort(403, $message);
        });
    }
}
