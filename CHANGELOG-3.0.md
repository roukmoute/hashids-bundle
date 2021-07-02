# CHANGELOG for 3.0.x

This changelog references the relevant changes done in 3.0 version.

### 3.0.0 (2021-03-26)

* This new version is only for PHP >=7.3.
* Bump all dependencies
* Replace `symfony/http-kernel` with `symfony/dependency-injection` and 
  `symfony/config` dependencies only
* Tag @ParamConverter not used anymore, it is completely automatic
* Fix problem with `{id/hashid}` in `@Route` and with multiple arguments in 
  method used for the `Route`. 
