# Usage example

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