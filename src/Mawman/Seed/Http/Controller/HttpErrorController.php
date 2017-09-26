<?php


namespace Mawman\Seed\Http\Controller;


class HttpErrorController extends Controller
{

    /**
     * @param int $code
     * @param null|string $message
     * @param null|\Exception $exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function onError($code, $message = null, $exception = null) {

        $viewException = null;
        if ($exception instanceof \Exception) {
            $viewException = array_map(function ($str) {
                return strtr($str, [
                    getenv("DB_PASSWORD") => "*REMOVED*",
                ]);
            }, [
                "message" => $exception->getMessage(),
                "code" => $exception->getCode(),
                "stackTrace" => $exception->getTraceAsString(),
            ]);
        }

        $this->render("errors/error_xxx.twig", [
            "code" => $code,
            "message" => $message,
            "exception" => $viewException,
        ]);
    }

}