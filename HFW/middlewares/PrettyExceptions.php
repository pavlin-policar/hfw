<?php

namespace hfw\middlewares;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PrettyExceptions
 *
 * @package hfw\middlewares
 */
class PrettyExceptions extends BaseMiddleware {

  /**
   * Handles a Request to convert it to a Response.
   *
   * When $catch is true, the implementation must catch all exceptions
   * and do its best to convert them to a Response instance.
   *
   * @param Request $request A Request instance
   * @param int     $type    The type of the request
   *                         (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
   * @param bool    $catch   Whether to catch exceptions or not
   *
   * @return Response A Response instance
   *
   * @throws \Exception When an Exception occurs during processing
   *
   * @api
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {
    try {
      return $this->_next->handle($request, $type, $catch);
    } catch (\Exception $e) {
      $response = new Response();
      $response->setStatusCode(500);
      $response->setContent($this->renderException($e));

      return $response;
    }
  }

  /**
   * Create a readable exception message, inspired by Slim framework PrettyExceptions
   *
   * @param \Exception $exception
   * @return string
   */
  protected function renderException(\Exception $exception) {
    $title = 'Internal application error';
    $code = $exception->getCode();
    $message = $exception->getMessage();
    $file = $exception->getFile();
    $line = $exception->getLine();
    $trace = htmlspecialchars($exception->getTraceAsString());

    $html = sprintf('<h1>%s</h1>', $title);
    $html .= sprintf('<p>The application has encountered the following error:</p>');
    $html .= sprintf('<h2>Details</h2>');
    $html .= sprintf('<div><strong>Type:</strong> %s</div>', get_class($exception));
    if ($code) {
      $html .= sprintf('<div><strong>Code:</strong> %s</div>', $code);
    }
    if ($message) {
      $html .= sprintf('<div><strong>Message:</strong> %s</div>', $message);
    }
    if ($file) {
      $html .= sprintf('<div><strong>File:</strong> %s</div>', $file);
    }
    if ($line) {
      $html .= sprintf('<div><strong>Line:</strong> %s</div>', $line);
    }
    if ($trace) {
      $html .= '<h2>Trace</h2>';
      $html .= sprintf('<pre>%s</pre>', $trace);
    }
    return sprintf("<html><head><title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{display:inline-block;width:65px;}</style></head><body>%s</body></html>",
        $title, $html);
  }
}