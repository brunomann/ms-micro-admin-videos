<?php

namespace Tests\Unit\App\Http\Controllers\Api;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\Api\CategoryController;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesOutputDto;
use Illuminate\Http\Request;
use Mockery;

class CategoryControllerUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $mockRequest = Mockery::mock(Request::class);
        $mockRequest->shouldReceive('get')->andReturn('teste');
        
        $mockDtoOutput = Mockery::mock(ListCategoriesOutputDto::class, [
            [], 1, 1, 1, 1, 1, 1, 1
        ]);

        $mockUsecase = Mockery::mock(ListCategoriesUseCase::class);
        $mockUsecase->shouldReceive('execute')->andReturn($mockDtoOutput);

        $controller = new CategoryController();
        $response = $controller->index($mockRequest, $mockUsecase);

        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);

        /**
         * Spies
         */
        $mockUsecaseSpies = Mockery::mock(ListCategoriesUseCase::class);
        $mockUsecaseSpies->shouldReceive('execute')->andReturn($mockDtoOutput);

        $controller->index($mockRequest, $mockUsecaseSpies);
        
        $mockUsecaseSpies->shouldHaveReceived('execute');

        Mockery::close();
    }
}
