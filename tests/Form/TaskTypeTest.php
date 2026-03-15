<?php
namespace App\Test\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase{
    public function testSubmitValidData(){
        $formData = [
            'title' => 'Test task',
            'description' => 'Task description',
            'status' => 'pending',
            'dueDate' => new \DateTime('+1 day'),
        ];

        $task = new Task();
        $form = $this->factory->create(TaskType::class, $task);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals('Test task', $task->getTitle());
        $this->assertEquals('Task description', $task->getDescription());
        $this->assertEquals('pending', $task->getStatus());
    }
}
?>