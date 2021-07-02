# UPGRADE FROM 2.x to 3.0

* `$this->get('hashids')` is not possible anymore since it is a bad practice.
Require it on parameter via HashidInterface
