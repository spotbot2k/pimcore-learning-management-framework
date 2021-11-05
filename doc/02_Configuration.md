# Configuration

This Framework does not provide any kind auf authentication but relies on it to track grades and progress of your students. 
The reason for that is that the framework is ment to be integrated into a bigger system that provides user management via [Customer Data Framework](https://github.com/pimcore/customer-data-framework), [Members Bundle](https://github.com/dachcom-digital/pimcore-members) or other method. To integrate it with your existing user class you need to write the name of the class in your config file:

``` yaml
pimcore_learning_management_framework:
    student_class: Student
```

For example - in the official Pimcore demo it would be `Customer`. The class itself needs to be part of "Pimcore\Model\DataObject" namespace. In other words - it must be a Pimcore data object.

After you have done that the `ExamHelper` will detect the logged-in user, track his progress and provide it within its context.