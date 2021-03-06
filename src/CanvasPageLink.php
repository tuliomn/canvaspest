<?php

/** smtech\CanvasPest\CanvasPageLink */

namespace smtech\CanvasPest;

/**
 * An object to represent Canvas API pagination information.
 *
 * As the whole point of CanvasPest is to abstract the actual API response out
 * of the way, concealing the mundane details of the response pagination, this
 * particular object is used only internally to create object-oriented access
 * to the API response's `link` header.
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasPageLink
{
    /** Name of the current page link */
    const CURRENT = 'current';

    /** Name of the first page link */
    const FIRST = 'first';

    /** Name of the last page link */
    const LAST = 'last';

    /** Name of the next page link */
    const NEXT = 'next';

    /** Name of the previous page link */
    const PREV = 'prev';


    /** @var string $name Name of the page link */
    private $name;

    /** @var string $endpoint Path of the API endpoint being paginated */
    private $endpoint;

    /** @var array $params Query parameters for the page link API call */
    private $params;

    /** Name of the page number parameter in the page link */
    const PARAM_PAGE_NUMBER = 'page';

    /** Name of the parameter describing the number of responses per page in the page link */
    const PARAM_PER_PAGE = 'per_page';

    /**
     * Construct a new Canvas page link object.
     *
     * CanvasPageLinks can be constructed with two possible parameter lists:
     *
     * @param string $pageUrl URL of the API endpoint to retrieve the page
     * @param string $pageName Canonical name of the page relative to the current
     *                     page.
     *
     * @throws CanvasPageLink_Exception INVALID_CONSTRUCTOR If $pageUrl or
     *         $pageName is empty or a non-string
     */
    public function __construct($pageUrl, $pageName)
    {
        $this->name = $pageName;
        if (is_string($pageUrl) && !empty($pageUrl) && is_string($this->name) && !empty($this->name)) {
            $this->endpoint = preg_replace('%.*/api/v1(/.*)$%', '$1', parse_url($pageUrl, PHP_URL_PATH));
            parse_str(parse_url($pageUrl, PHP_URL_QUERY), $this->params);
        } else {
            throw new CanvasPageLink_Exception(
                'Expected two non-empty strings for page URL and name',
                CanvasPageLink_Exception::INVALID_CONSTRUCTOR
            );
        }
    }

    /**
     * Canonical name of this page link
     *
     * @return string
     **/
    public function getName()
    {
        return $this->name;
    }

    /**
     * API endpoint being paginated
     *
     * @return string
     **/
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Query parameters to retrieve the linked page
     *
     * @return array
     **/
    public function getParams()
    {
        return $this->params;
    }

    /**
     * The (1-indexed) page number of this page
     *
     * @return int
     **/
    public function getPageNumber()
    {
        return $this->params[self::PARAM_PAGE_NUMBER];
    }

    /**
     * The number of responses per page generating this pagination
     *
     * @return int
     **/
    public function getPerPage()
    {
        return $this->params[self::PARAM_PER_PAGE];
    }
}
