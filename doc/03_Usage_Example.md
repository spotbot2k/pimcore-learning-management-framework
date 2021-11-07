# Usage example

There is a minimal difference between the two major versions of Pimcore - here are the examples of usage in a structure of the official Pimcore demo.

- [Basic usage for Pimcore X](03_Usage_Example.md#basic-usage-for-pimcore-x)
- [Basic usage for Pimcore 6.9](03_Usage_Example.md#basic-usage-for-pimcore-6.9)

## Basic usage for Pimcore X

1. [Install](01_Installation.md) the bundle
2. Create your first exam data object
3. Write a controller to render the exam:

`src/Controller/LMFController.php`

``` php
<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\ExamDefinition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use LearningManagementFrameworkBundle\Helper\ExamHelper;
use LearningManagementFrameworkBundle\Form\ExamType;

class LMFController extends FrontendController
{
    /**
     * @Route("/lmf/form/{id}", name="lmf-test-form")
     */
    public function testForm(Request $req, int $id, ExamHelper $helper)
    {
        $exam = ExamDefinition::getById($id);
        $form = $this->createForm(ExamType::class, $exam);
        $result = null;

        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $helper->process($exam, $req->get(ExamType::PREFIX));
        }

        return $this->renderForm('lmf/form.html.twig', [
            'form'   => $form,
            'exam'   => $exam,
            'result' => $result,
        ]);
    }
}
```
4. Create the twig template

`templates/lmf/form.html.twig`

``` twig
<div id="main-content" class="main-content mb-5">
    <div class="page-header">
        <h1>{{ exam.getTitle() }}</h1>
    </div>

    {{ exam.getDescription() | raw }}

    {{ form(form) }}

    {% if result is not null %}
        {% if result['grade'] is not null %}
            Passed with {{ result['grade'] }}
        {% else %}
            Not passed
        {% endif %}
    {% endif %}
</div>
```
5. Try it out

## Basic usage for Pimcore 6.9

1. [Install](01_Installation.md) the bundle
2. Create your first exam data object
3. Write a controller to render the exam:

`src/AppBundle/Controller/LMFController.php`

``` php
<?php

namespace AppBundle\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\ExamDefinition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use LearningManagementFrameworkBundle\Helper\ExamHelper;
use LearningManagementFrameworkBundle\Form\ExamType;

class LMFController extends FrontendController
{
    /**
     * @Route("/lmf/form/{id}", name="lmf-test-form")
     */
    public function testForm(Request $req, int $id, ExamHelper $helper)
    {
        $exam = ExamDefinition::getById($id);
        $form = $this->createForm(ExamType::class, $exam);
        $result = null;

        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $helper->process($exam, $req->get(ExamType::PREFIX));
        }

        return $this->render('lmf/form.html.twig', [
            'form'   => $form->createView(),
            'exam'   => $exam,
            'result' => $result,
        ]);
    }
}
```
4. Create the twig template

`app/Resources/views/lmf/form.html.twig`

``` twig
<div id="main-content" class="main-content mb-5">
    <div class="page-header">
        <h1>{{ exam.getTitle() }}</h1>
    </div>

    {{ exam.getDescription() | raw }}

    {{ form(form) }}

    {% if result is not null %}
        {% if result['grade'] is not null %}
            Passed with {{ result['grade'] }}
        {% else %}
            Not passed
        {% endif %}
    {% endif %}
</div>
```
5. Try it out