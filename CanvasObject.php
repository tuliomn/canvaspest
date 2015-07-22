<?php

/** CanvasObject and related classes */

/**
 * An object that represents a single Canvas object, providing both object-
 * style access (obj->key) and array-style access (array[key]).
 * CanvasObject objects are immutable, so attempts to change their
 * underlying data will result in exceptions.
 *
 * A CanvasObject is returned from any API request for which the endpoint ends
 * with a specific ID number (e.g. http://example.com/api/v1/accounts/1/users/1).
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasObject implements ArrayAccess {
	
	/** @var array $data Backing store */
	private $data;
	
	/**
	 * Construct a CanvasObject
	 *
	 * @param string|string[] $response JSON-encoded response from the Canvas API or the resulting JSON-decoded associative array
	 *
	 * @return void
	 **/
	public function __construct($response) {
		if (is_array($response)) {
			$this->data = $response;
		} else {
			$this->data = json_decode($jsonResponse, true);
		}
	}
	
	/****************************************************************************
	 Object methods */

	/**
	 * Whether a property exists
	 *
	 * @param string $key
	 *
	 * @return bool
	 *
	 * @see http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members Property overloading
	 **/
	public function __isset($key) {
		return isset($this->data[$key]);
	}
	
	/**
	 * Property to retrieve
	 *
	 * @param string $key
	 *
	 * @return mixed
	 *
	 * @see http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members Property overloading
	 **/
	public function __get($key) {
		return $this->data[$key];
	}

	/**
	 * Whether a property exists
	 *
	 * @deprecated Canvas objects are immutable
	 *
	 * @return void
	 *
	 * @throws CanvasObject_Exception IMMUTABLE All calls to this method will cause an exception
	 *
	 * @see http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members Property overloading
	 **/
	public function __set() {
		throw new CanvasObject_Exception(
			'Canvas objects are immutable',
			CanvasObject_Exception::IMMUTABLE
		);
	}
	
	/**
	 * Unset a property
	 *
	 * @deprecated Canvas objects are immutable
	 *
	 * @return void
	 *
	 * @throws CanvasObject_Exception IMMUTABLE All calls to this method will cause an exception
	 *
	 * @see http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members Property overloading
	 **/
	public function __unset() {
		throw new CanvasObject_Exception(
			'Canvas objects are immutable',
			CanvasObject_Exception::IMMUTABLE
		);
	}
	
	/****************************************************************************/
	
	/****************************************************************************
	 ArrayAccess methods */
	
	/**
	 * Whether an offset exists
	 *
	 * @param int|string $offset
	 *
	 * @return bool
	 *
	 * @see http://php.net/manual/en/arrayaccess.offsetexists.php ArrayAccess::offsetExists
	 **/
	public function offsetExists ($offset) {
		return isset($this->data[$offset]);
	}
	
	/**
	 * Offset to retrieve
	 *
	 * @param int|string $offset
	 *
	 * @return mixed|null
	 *
	 * @see http://php.net/manual/en/arrayaccess.offsetexists.php ArrayAccess::offsetGet
	 **/
	public function offsetGet ($offset) {
		return $this->data[$offset];
	}
	
	/**
	 * Assign a value to the specified offset
	 *
	 * @deprecated Canvas objects are immutable
	 *
	 * @return void
	 *
	 * @throws CanvasObject_Exception IMMUTABLE All calls to this method will cause an exception
	 *
	 * @see http://php.net/manual/en/arrayaccess.offsetset.php ArrayAccess::offsetSet
	 **/
	public function offsetSet() {
		throw new CanvasObject_Exception(
			'Canvas objects are immutable',
			CanvasObject_Exception::IMMUTABLE
		);
	}
	
	/**
	 * Unset an offset
	 *
	 * @deprecated Canvas objects are immutable
	 *
	 * @return void
	 *
	 * @throws CanvasObject_Exception IMMUTABLE All calls to this method will cause an exception
	 *
	 * @see http://php.net/manual/en/arrayaccess.offsetunset.php ArrayAccess::offsetUnset
	 **/
	public function offsetUnset () {
		throw new CanvasObject_Exception(
			'Canvas objects are immutable',
			CanvasObject_Exception::IMMUTABLE
		);
	}
	
	/****************************************************************************/
}

/**
 * All exceptions thrown by CanvasObject
 *
 * @author Seth Battis <SethBattis@stmarksschool.org>
 **/
class CanvasObject_Exception extends CanvasPest_Exception {
	/** Response values are read-only */
	const IMMUTABLE = 101;
}

?>