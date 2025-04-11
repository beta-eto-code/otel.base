# OpenTelemetry lib

**Пример описания HTTP контроллера:**

```php
use Otel\Base\Util\RequestHelper;
use Psr\Http\Server\RequestHandlerInterface;


class SomeHttpController implements RequestHandlerInterface 
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $spanManager = RequestHelper::getSpanManagerFromRequest($request);
        $spanManager->getSpan()->setAttribute('someSpanAttribute', 'someValue')
        $spanManager->getSpan()->addEvent(
            'startController', 
            ['firstEventAttribute' => 1, 'secondEventAttribute' => 2]
        );
        
        ....
    }
}
```


**Абстрактный пример инициализации REST API приложения с интеграцией OpenTelemetry**
```php
use Otel\Base\OTelMiddleware;
use Otel\Base\OTelFactory;

$oTelFactory = new OTelFactory('./otel.json');
$oTelMiddleware = OTelMiddleware::initWithFactory($oTelFactory);
$someRestApplication->registerMiddleware($oTelMiddleware);
$router = $someRestApplication->getRouter();
$router->registerController('GET', '/api/handle', new SomeHttpController());

$someRestApplication->run();
```