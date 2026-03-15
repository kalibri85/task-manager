<?php
namespace App\Entity\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

// Test basic getters and setters of Task entity
class TaskEntityTest extends TestCase{
    public function testTaskEntity(): void {
        $task = new Task();

        $task->setTitle('Test task');
        $task->setDescription('Test description');
        $task->setStatus('pending');
        $task->setDueDate(new \DateTime('+1 day'));

        $this->assertEquals('Test task', $task->getTitle());
        $this->assertEquals('Test description', $task->getDescription());
        $this->assertEquals('pending', $task->getStatus());
        $this->assertInstanceOf(\DateTimeInterface::class, $task->getDueDate());
    }
}
?>