<?php
namespace Aoe\Restler\Http;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Aoe\Restler\System\Dispatcher;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Http\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * This is the main entry point of the TypoScript driven standard front-end
 *
 * Basically put, this is the script which all requests for TYPO3 delivered pages goes to in the
 * frontend (the website). The script instantiates a $TSFE object, includes libraries and does a little logic here
 * and there in order to instantiate the right classes to create the webpage.
 * Previously, this was called index_ts.php and also included the logic for the lightweight "eID" concept,
 * which is now handled in a separate request handler (EidRequestHandler).
 */
class RestRequestHandler implements RequestHandlerInterface
{
    /**
     * Instance of the current TYPO3 bootstrap
     * @var Bootstrap
     */
    protected $bootstrap;

    /**
     * The request handed over
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * Constructor handing over the bootstrap and the original request
     *
     * @param Bootstrap $bootstrap
     */
    public function __construct(Bootstrap $bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    /**
     * Handles a frontend request
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return NULL|\Psr\Http\Message\ResponseInterface
     */
    public function handleRequest(\Psr\Http\Message\ServerRequestInterface $request)
    {
        // We define this constant, so that any TYPO3-Extension can check, if the REST-API is running
        define('REST_API_IS_RUNNING', true);

        // Dispatch the API-call
        GeneralUtility::makeInstance(Dispatcher::class)->dispatch();
    }

    /**
     * This request handler can handle any frontend request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return bool If the request is not an eID request, TRUE otherwise FALSE
     */
    public function canHandleRequest(\Psr\Http\Message\ServerRequestInterface $request)
    {
        return strpos($request->getUri()->getPath(), '/api/') === 0;
    }

    /**
     * Returns the priority - how eager the handler is to actually handle the
     * request.
     *
     * @return int The priority of the request handler.
     */
    public function getPriority()
    {
        return 60;
    }
}
