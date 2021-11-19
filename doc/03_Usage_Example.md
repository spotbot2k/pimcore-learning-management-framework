# Usage example

There is a minimal difference between the two major versions of Pimcore - here are the examples of usage in a structure of the official Pimcore demo.

- [Basic usage for Pimcore 6.9](03_Usage_Example.md#basic-usage-for-pimcore-69)
- [Basic usage for Pimcore X](03_Usage_Example.md#basic-usage-for-pimcore-x)

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
     * @Route("/lmf/exam/{id}", name="lmf-exam-form")
     */
    public function examForm(Request $request, int $id, ExamHelper $helper)
    {
        $exam = ExamDefinition::getById($id);
        $canAttend = $helper->canAttend($exam);
        $form = $this->createForm(ExamType::class, $exam);
        $attemptsLeft = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $helper->process($exam, $request->get(ExamType::PREFIX));

            if ($result->isPassed) {
                return $this->redirectToRoute("lmf-exam-cert", [ "id" => $id, "hash" => $result->hash ]);
            }

            return $this->redirectToRoute("lmf-exam-form", [ "id" => $id, "ratio" => $result->ratio ]);
        }

        if ($exam->getMaxAttempts() && $exam->getMaxAttempts() > 0) {
            $attemptsLeft = $exam->getMaxAttempts() - $helper->getAttemptsCountForCurrentUser($exam);
        }

        return $this->render('lmf/form.html.twig', [
            'form'      => $form->createView(),
            'exam'      => $exam,
            'canAttend' => $canAttend,
            'student'   => $helper->getStudent(),
            'attempts'  => $attemptsLeft,
            'ratio'     => $request->get("ratio"),
            'editmode'  => false,
        ]);
    }

    /**
     * @Route("/lmf/exam/{id}/{hash}", name="lmf-exam-cert")
     */
    public function validateCertifficate(Request $req, int $id, string $hash, ExamHelper $helper)
    {
        $exam = ExamDefinition::getById($id);
        $result = $helper->validateCertificate($hash);

        return $this->render('lmf/cert.html.twig', [
            'exam'      => $exam,
            'result'    => $result,
            'editmode'  => false,
        ]);
    }
}
```
4. Create the twig template

`app/Resources/views/lmf/form.html.twig`

``` twig
{% extends 'layouts/layout.html.twig' %}
{% form_theme form 'bootstrap_4_layout.html.twig' %}

{% block content %}
    <div id="main-content" class="main-content mb-5">
        <div class="page-header">
            <h1>{{ exam.getTitle() }}</h1>
        </div>

        {{ exam.getDescription() | raw }}

        {% if canAttend.isRejected() %}
            <div class="alert alert-danger" role="alert">
                {{ "You can not attend. Reason: " | trans }} {{ canAttend.getReason() | trans }}

                {% set testsLeft = canAttend.getUnfulfilledPrerequisites() %}
                {% if testsLeft is not empty %}
                    <p>You need to pass:</p>
                    {% for test in testsLeft %}
                        <a href="/lmf/form/{{ test.id }}">{{ test.title }}</a><br>
                    {% endfor %}
                {% endif %}
            </div>
        {% else %}
            {% if ratio is not null %}
                <div class="alert alert-danger" role="alert">
                    You have not passed - only <b>{{ ratio }}%</b> of your answers were correct.
                    {% if attempts is not null %}
                        You have {{ attempts }} attempt(s) left.
                    {% endif %}
                </div>
            {% endif %}
            {{ form(form) }}
        {% endif %}
    </div>
{%  endblock %}
```
5. Add the second template

`app/Resources/views/lmf/cert.html.twig`

``` twig
{% extends 'layouts/layout.html.twig' %}

{% block content %}
    <div id="main-content" class="main-content mb-5">
        {% if result is defined and result.isValid() %}
            <div class="page-header">
                <h1>Congratulations!</h1>
            </div>
            <div class="lmf-cert pt-5 pb-5" style="border: 5px solid red;">
                <center>
                    <p>You have passed <b>"{{ exam.getTitle() }}"</b>, well done!</p>
                    <p>with <b>"{{ result.get('grade') }}"</b></p>
                    <p><b>{{ result.get('ratio') }}%</b> of your answers were correct!</p>
                    {% if exam.getPublicCertificate() %}
                        <p>{{ url("lmf-exam-cert", { "id": result.get("examId"), "hash": result.get("uuid") }) }}</p>
                    {% endif %}
                </center>
            </div>
        {% else %}
            <div class="page-header">
                <h1>Sorry!</h1>
            </div>
            <p>It seems not to be a valid certificate</p>
        {% endif %}
    </div>
{% endblock %}
```

6. Try it out

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
     * @Route("/lmf/exam/{id}", name="lmf-exam-form")
     */
    public function examForm(Request $request, int $id, ExamHelper $helper)
    {
        $exam = ExamDefinition::getById($id);
        $canAttend = $helper->canAttend($exam);
        $form = $this->createForm(ExamType::class, $exam);
        $attemptsLeft = null;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $helper->process($exam, $request->get(ExamType::PREFIX));

            if ($result->isPassed) {
                return $this->redirectToRoute("lmf-exam-cert", [ "id" => $id, "hash" => $result->hash ]);
            }

            return $this->redirectToRoute("lmf-exam-form", [ "id" => $id, "ratio" => $result->ratio ]);
        }

        if ($exam->getMaxAttempts() && $exam->getMaxAttempts() > 0) {
            $attemptsLeft = $exam->getMaxAttempts() - $helper->getAttemptsCountForCurrentUser($exam);
        }

        return $this->render('lmf/form.html.twig', [
            'form'      => $form->createView(),
            'exam'      => $exam,
            'canAttend' => $canAttend,
            'student'   => $helper->getStudent(),
            'attempts'  => $attemptsLeft,
            'ratio'     => $request->get("ratio"),
            'editmode'  => false,
        ]);
    }

    /**
     * @Route("/lmf/exam/{id}/{hash}", name="lmf-exam-cert")
     */
    public function validateCertifficate(Request $req, int $id, string $hash, ExamHelper $helper)
    {
        $exam = ExamDefinition::getById($id);
        $result = $helper->validateCertificate($hash);

        return $this->render('lmf/cert.html.twig', [
            'exam'      => $exam,
            'result'    => $result,
            'editmode'  => false,
        ]);
    }
}

```
4. Create the twig template

`templates/lmf/form.html.twig`

``` twig
{% extends 'layouts/layout.html.twig' %}
{% form_theme form 'bootstrap_4_layout.html.twig' %}

{% block content %}
    <div id="main-content" class="main-content mb-5">
        <div class="page-header">
            <h1>{{ exam.getTitle() }}</h1>
        </div>

        {{ exam.getDescription() | raw }}

        {% if canAttend.isRejected() %}
            <div class="alert alert-danger" role="alert">
                {{ "You can not attend. Reason: " | trans }} {{ canAttend.getReason() | trans }}

                {% set testsLeft = canAttend.getUnfulfilledPrerequisites() %}
                {% if testsLeft is not empty %}
                    <p>You need to pass:</p>
                    {% for test in testsLeft %}
                        <a href="/lmf/form/{{ test.id }}">{{ test.title }}</a><br>
                    {% endfor %}
                {% endif %}
            </div>
        {% else %}
            {% if ratio is not null %}
                <div class="alert alert-danger" role="alert">
                    You have not passed - only <b>{{ ratio }}%</b> of your answers were correct.
                    {% if attempts is not null %}
                        You have {{ attempts }} attempt(s) left.
                    {% endif %}
                </div>
            {% endif %}
            {{ form(form) }}
        {% endif %}
    </div>
{%  endblock %}
```
5. Add the second template

`app/Resources/views/lmf/cert.html.twig`

``` twig
{% extends 'layouts/layout.html.twig' %}

{% block content %}
    <div id="main-content" class="main-content mb-5">
        {% if result is defined and result.isValid() %}
            <div class="page-header">
                <h1>Congratulations!</h1>
            </div>
            <div class="lmf-cert pt-5 pb-5" style="border: 5px solid red;">
                <center>
                    <p>You have passed <b>"{{ exam.getTitle() }}"</b>, well done!</p>
                    <p>with <b>"{{ result.get('grade') }}"</b></p>
                    <p><b>{{ result.get('ratio') }}%</b> of your answers were correct!</p>
                    {% if exam.getPublicCertificate() %}
                        <p>{{ url("lmf-exam-cert", { "id": result.get("examId"), "hash": result.get("uuid") }) }}</p>
                    {% endif %}
                </center>
            </div>
        {% else %}
            <div class="page-header">
                <h1>Sorry!</h1>
            </div>
            <p>It seems not to be a valid certificate</p>
        {% endif %}
    </div>
{% endblock %}
```

6. Try it out