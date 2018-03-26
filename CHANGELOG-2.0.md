# CHANGELOG for 2.0.x

This changelog references the relevant changes done in 2.0 version.

### 2.0.0(2018-03-26)

* This new version is only for PHP ⩾7.1.
* Update all dependencies (remove all doctrine dependencies)
* DoctrineParamConverter is not used anymore
* This bundle only converts hashid into an id
* Replace `autowire` with `passthrough`.  
This new parameter which allows to continue with the next param converters available.
