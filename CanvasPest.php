<?php

/**
 * An object to handle interactions with the Canvas API.
 * 
 * For more information on the Canvas API refer to the offical
 * {@link https://canvas.instructure.com/doc/api/ Canvas API documentation} or
 * to the (slightly more up-to-date and pleasingly interactive)
 * {@link https://canvas.instructure.com/doc/api/live live documentation}.
 *
 * You can access the live documentation for your own Canvas instance and make
 * actual API calls to it at `https://<path-to-your-instance>/doc/api/live`
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasPest extends Pest {
	
	/** Name of the parameter controlling the number of responses per page */
	const PARAM_PER_PAGE = 'per_page';
	
	/** @var array $headers Additional headers to be passed to the API with each call */
	protected $headers;
	
	/**
	 * @api
	 *
	 * @param string $apiInstanceUrl URL of the API instance (e.g. `'https://canvas.instructure.com/api/v1'`)
	 * @param string $apiAuthorizationToken (Optional) API access token for the API instance (if not provided now, it will need to be provided later)
	 * @see CanvasPest::setupToken() To configure the API access token later
	 **/
	public function __construct($apiInstanceUrl, $apiAuthorizationToken = null) {
		parent::__construct($apiInstanceUrl);
		if (!empty($apiAuthorizationToken)) {
			$this->setupToken($apiAuthorizationToken);
		}
	}
	
	/**
	 * Set up a new API access token to access this instance.
	 *
	 * @param string $token API access token
	 *
	 * @throws CanvasPest_Exception INVALID_TOKEN on an empty or non-string token value
	 **/
	public function setupToken($token) {
		if (is_string($token) && !empty($token)) {
			$this->headers['Authorization'] = "Bearer $token";
		} else if ($this->throw_exceptions) {
			throw new CanvasPest_Exception(
				'API authorization token must be a non-zero-length string',
				CanvasPest_Exception::INVALID_TOKEN
			);
		}
	}
	
	/**
	 * Preprocess API call parameters before use.
	 *
	 * Force maximum response page size, if not already defined.
	 *
	 * @param array $data Array of parameters for the next API call
	 *
	 * @return array Updated array of parameters
	 *
	 * @see CanvasArray::MAXIMUM_PER_PAGE Maximum number of responses per page
	 * @see CanvasPest::PARAM_PER_PAGE Page size parameter
	 **/
	private function preprocessData($data) {
		if (is_array($data) && !array_key_exists(self::PARAM_PER_PAGE, $data)) {
			$data[self::PARAM_PER_PAGE] = CanvasArray::MAXIMUM_PER_PAGE;
		}
		return $data;
	}
	
	/**
     * Prepare data.
     *
     * Extended by CanvasPest to format the HTTP query parameters
      with non-indexed array elements (so `...foo?bar[]=1&bar[]=2`, rather than
     * `...foo?bar[0]=1&bar[1]=2`, as the Canvas API prefers them).
     *
     * @param array $data Query parameters
     *
     * @return array|string
     */
    public function prepData($data)
    {
        if (is_array($data)) {
            $multipart = false;

            foreach ($data as $item) {
                if (is_string($item) && strncmp($item, "@", 1) == 0 && is_file(substr($item, 1))) {
                    $multipart = true;
                    break;
                }
            }

			// FIXME there has _got_ to a better way to do this than a regex
            return ($multipart) ? $data : preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', http_build_query($data));
        } else {
            return $data;
        }
    }

	/**
     * Flatten headers from an associative array to a numerically indexed array of "Name: Value" style entries like CURLOPT_HTTPHEADER expects. Numerically indexed arrays are not modified.
     *
     * Extended by CanvasPest to include the API access token in the Authorization header.
     *
     * @param array $headers
     * @return array
	 **/
	protected function prepHeaders($headers) {
		return parent::prepHeaders(array_merge($this->headers, $headers));
	}
	
	/**
	 * Parse the API response into an object (or collection of objects).
	 *
	 * @param string $path Path to the API endpoint queried
	 * @param string $response JSON-encoded response from the API
	 *
	 * @return CanvasObject|CanvasArray For queries to individually identified endpoints (e.g. `accounts/1/users/123`), return a CanvasObject representing the API response describing _that_ individually identified object affected by the query. For queries to generic endpoints (e.g. `accounts/1/users`), return a traversable CanvasArray (of CanvasObjects) representing the API response describing the list of objects affected by the query.
	 **/
	protected function postprocessResponse($path, $response) {
		if(preg_match('%^.*/\d+/?$%', $path)) {
			return new CanvasObject($response, $this);
		} else {
			return new CanvasArray($response, $this);
		}
	}
	
	/**
	 * Make a GET call to the API
	 *
	 * @api
	 *
	 * @param string $path Path to the API endpoint of this call
	 * @param string|array $data Query parameters for this call
	 * @param string|array $headers Any additional HTTP headers for this call
	 *
	 * @return CanvasObject|CanvasArray For queries to individually identified endpoints (e.g. `accounts/1/users/123`), return a CanvasObject representing the API response describing _that_ individually identified object affected by the query. For queries to generic endpoints (e.g. `accounts/1/users`), return a traversable CanvasArray (of CanvasObjects) representing the API response describing the list of objects affected by the query.
	 **/
	public function get($path, $data = array(), $headers = array()) {
		return $this->postprocessResponse(
			$path,
			parent::get($path, $this->preprocessData($data), $headers)
		);
	}
	
	/**
	 * Make a POST call to the API
	 *
	 * @api
	 *
	 * @param string $path Path to the API endpoint of this call
	 * @param string|array $data Query parameters for this call
	 * @param string|array $headers Any additional HTTP headers for this call
	 *
	 * @return CanvasObject|CanvasArray For queries to individually identified endpoints (e.g. `accounts/1/users/123`), return a CanvasObject representing the API response describing _that_ individually identified object affected by the query. For queries to generic endpoints (e.g. `accounts/1/users`), return a traversable CanvasArray (of CanvasObjects) representing the API response describing the list of objects affected by the query.
	 **/
	public function post($path, $data = array(), $headers = array()) {
		return $this->postprocessResponse(
			$path,
			parent::post($path, $this->preprocessData($data), $headers)
		);
	}
	
	/**
	 * Make a PUT call to the API
	 *
	 * @api
	 *
	 * @param string $path Path to the API endpoint of this call
	 * @param string|array $data Query parameters for this call
	 * @param string|array $headers Any additional HTTP headers for this call
	 *
	 * @return CanvasObject|CanvasArray For queries to individually identified endpoints (e.g. `accounts/1/users/123`), return a CanvasObject representing the API response describing _that_ individually identified object affected by the query. For queries to generic endpoints (e.g. `accounts/1/users`), return a traversable CanvasArray (of CanvasObjects) representing the API response describing the list of objects affected by the query.
	 **/
	public function put($path, $data = array(), $headers = array()) {
		return $this->postprocessResponse(
			$path,
			parent::put($path, $this->preprocessData($data), $headers)
		);
	}
		
	/**
	 * Make a DELETE call to the API
	 *
	 * @api
	 *
	 * @param string $path Path to the API endpoint of this call
	 * @param string|array $data Query parameters for this call
	 * @param string|array $headers Any additional HTTP headers for this call
	 *
	 * @return CanvasObject|CanvasArray For queries to individually identified endpoints (e.g. `accounts/1/users/123`), return a CanvasObject representing the API response describing _that_ individually identified object affected by the query. For queries to generic endpoints (e.g. `accounts/1/users`), return a traversable CanvasArray (of CanvasObjects) representing the API response describing the list of objects affected by the query.
	 **/
	public function delete($path, $headers = array()) {
		return $this->postprocessResponse(
			$path,
			parent::delete($path, $headers)
		);
	}

	/**
	 * Overrides Pest::patch() to prevent accidental calls.
	 * 
	 * @deprecated The Canvas API does not currently support PATCH calls
	 *
	 * @throws CanvasPest_Exception UNSUPPORTED_METHOD All calls to this method will cause an exception
	 **/	
	public function patch($path, $data = array(), $headers = array()) {
		throw new CanvasPest_Exception(
			'The Canvas API does not support the PATCH method',
			CanvasPest_Exception::UNSUPPORTED_METHOD
		);
	}
}

/**
 * All exceptions thrown by the CanvasPest object
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/	
class CanvasPest_Exception extends Exception {
	/** The API access method is not supported by the Canvas API */
	const UNSUPPORTED_METHOD = 1;
	
	/** The API access token provided is invalid */
	const INVALID_TOKEN = 2;
}

?>