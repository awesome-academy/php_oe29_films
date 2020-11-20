<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Backend\ChartController;
use App\Models\Type;
use Tests\TestCase;
use Illuminate\Http\RedirectResponse;
use App\Repositories\Type\TypeRepositoryInterface;
use Mockery;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class TypeControllerTest extends TestCase
{
    use WithoutMiddleware;

    protected $type;

    public function setUp(): void
    {
        $this->typeMock = Mockery::mock(TypeRepositoryInterface::class);
        $this->chartController = new ChartController(
            $this->typeMock,
        );
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->chartController);
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_function()
    {
        $this->typeMock
            ->shouldReceive('getCountOfMovieFromType')
            ->once()
            ->andReturn([]);

        $result = $this->chartController->index();
        $this->assertEquals('backend.chart', $result->getName());
        $data = $result->getData();
        $this->assertArrayHasKey('types', $data);
    }
}
