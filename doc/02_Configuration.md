# Configuration

This Framework does not provide any kind auf authentication but relies on it to track grades and progress of your students. 
The reason for that is that the framework is ment to be integrated into a bigger system that provides user management via [Customer Data Framework](https://github.com/pimcore/customer-data-framework), [Members Bundle](https://github.com/dachcom-digital/pimcore-members) or other implementation. To integrate it with your existing user class you need to write the name of the class in your config file:

``` yaml
pimcore_learning_management_framework:
    student_class: Student
```

For example - in the official Pimcore demo it would be `Customer`., the default name in MembersBundle is `MemberUser`. The class itself needs to be part of "Pimcore\Model\DataObject" namespace. In other words - it must be a Pimcore data object.

After you have done that the `ExamHelper` will detect the logged-in user, track his progress and provide it within its context.

Other options are

``` yaml
pimcore_learning_management_framework:
    student_name_property: email
    attempt_reset_period: 7
```
`student_name_property` - Parameter to be used to fetch the readable name of the student. By default it is "email". It is used as a part of an SQL-query, so something like `CONCAT(name, ', ', lastname)` is legitimate. And yes, you can do an SQL-Injection with that, but having write permissions on server you could do it anyway.

`attempt_reset_period` - The period in days for an attempt to be reseted. Say you have 3 attempts for a test, after using them up you will have a new chase after this period of time.
