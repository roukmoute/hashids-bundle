# UPGRADE FROM 1.x to 2.0

### Configuration

* Removed the `autowire` parameter in favor of the `passthrough` parameter.

Before:

```yml
roukmoute_hashids:
    autowire: false
```

After:

```yml
roukmoute_hashids:
    passthrough: false
```

### HashidsParamConverter

* Replace `id` key option with `hashid`.

Before:

```php
/**
 * @Route("/users/{userHashid}/status/{statusHashid}")
 * @ParamConverter("user", class="RoukmouteBundle\Entity\User", options={"id" = "userHashid"})
 * @ParamConverter("status", class="RoukmouteBundle\Entity\Notification", options={"id" = "statusHashid"})
 */
public function getAction(User $user, Status $status)
{
}
```

After:

```php
/**
 * @Route("/users/{userHashid}/status/{statusHashid}")
 * @ParamConverter("user", class="RoukmouteBundle\Entity\User", options={"hashid" = "userHashid"})
 * @ParamConverter("status", class="RoukmouteBundle\Entity\Notification", options={"hashid" = "statusHashid"})
 */
public function getAction(User $user, Status $status)
{
}
```

* Remove the requirement to have `Hashid` at the end of the value option

Before:

```php
/**
 * @Route("/users/{userHashid}/status/{statusHashid}")
 * @ParamConverter("user", class="RoukmouteBundle\Entity\User", options={"id" = "userHashid"})
 * @ParamConverter("status", class="RoukmouteBundle\Entity\Notification", options={"id" = "statusHashid"})
 */
public function getAction(User $user, Status $status)
{
}
```

After:

```php
/**
 * @Route("/users/{user}/status/{status}")
 * @ParamConverter("user", class="RoukmouteBundle\Entity\User", options={"hashid" = "user"})
 * @ParamConverter("status", class="RoukmouteBundle\Entity\Notification", options={"hashid" = "status"})
 */
public function getAction(User $user, Status $status)
{
}
```
