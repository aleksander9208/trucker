<?php

declare(strict_types=1);

namespace Taxcom\Library\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Request;
use Taxcom\Library\Trait\ConfigureActionsTrait;

/**
 * Базовый класс для работы котроллера
 */
class BaseController extends Controller
{
    use ConfigureActionsTrait;

    /**
     * @param Request|null $request
     */
    public function __construct(Request $request = null)
    {
         parent::__construct($request);
    }

    /**
     * @return array
     */
    public function configureActions(): array
    {
        $result = $this->unsetHttpMethodPrefilter();
//        $result = $this->configureGetActions($result);
//        $result = $this->configureAddActions($result);
//        $result = $this->configureUpdateActions($result);
//        $result = $this->configureDeleteActions($result);

        return $result;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !$this->errorCollection->isEmpty();
    }
}