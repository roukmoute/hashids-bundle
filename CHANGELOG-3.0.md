# CHANGELOG for 3.0.x

This changelog references the relevant changes (bug and security fixes) done in 3.0 version.

### 3.0.0 (2021-03-26)

* This new version is only for PHP >=7.4.
* Bump all dependencies
* Tag @ParamConverter not used anymore.
* Fix #8 problem with `{id/hashid}` in `@Route` and with multiple arguments in 
  method used for the `Route`. 
* Feature #16 Add `LogicException` when hash value could not be decoded.
* No more extra feature for `Hashids`
