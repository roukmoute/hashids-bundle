# UPGRADE FROM 2.x to 3.0

* `$this->get('hashids')` is not possible anymore since it is a bad practice.
Require it on parameter via `Hashids\HashidsInterface`.
* The `_hash_` prefix must be added to force the use of the bundle. 
Or you can use the new `auto_convert` option to try to convert all 
the controller parameters.
* Replace with a new Hashids service.
* Removed the setMinHashLength() method in favor of creating new hashids service.
* Removed the encodeWithCustomHashLength() method in favor of creating new hashids service.
