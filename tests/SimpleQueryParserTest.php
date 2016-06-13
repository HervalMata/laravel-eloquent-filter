<?php

namespace Mnabialek\LaravelEloquentFilter\Tests;

use Illuminate\Support\Collection;
use Mnabialek\LaravelEloquentFilter\Filter;
use Mnabialek\LaravelEloquentFilter\Parsers\SimpleQueryParser;
use Mnabialek\LaravelEloquentFilter\Sort;
use Mockery as m;

class SimpleQueryParserTest extends UnitTestCase
{
    /** @test */
    public function it_returns_empty_filters_when_empty_request()
    {
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('except')->once()->with('sort')->andReturn([]);

        $parser = new SimpleQueryParser($request, new Collection());
        $this->assertEquals(new Collection(), $parser->getFilters());
    }

    /** @test */
    public function it_returns_valid_filters_when_not_empty_request()
    {
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('except')->once()->with('sort')->andReturn([
            'id' => 5,
            'email' => '  test@example.com ',
            'something' => ['  foo  ', 'bar', 'baz'],
        ]);

        $filters = new Collection([
            new Filter('id', 5),
            new Filter('email', '  test@example.com '),
            new Filter('something', ['  foo  ', 'bar', 'baz']),
        ]);

        $parser = new SimpleQueryParser($request, new Collection());
        $this->assertEquals($filters, $parser->getFilters());
    }

    /** @test */
    public function it_returns_empty_sorts_when_empty_request()
    {
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('input')->once()->with('sort', '')
            ->andReturn('');

        $parser = new SimpleQueryParser($request, new Collection());
        $this->assertEquals(new Collection(), $parser->getSorts());
    }

    /** @test */
    public function it_returns_valid_sorts_when_empty_request()
    {
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('input')->once()
            ->with('sort', '')->andReturn('id,-email,foo,bar,baz');

        $sorts = new Collection([
            new Sort('id', 'ASC'),
            new Sort('email', 'DESC'),
            new Sort('foo', 'ASC'),
            new Sort('bar', 'ASC'),
            new Sort('baz', 'ASC'),
        ]);

        $parser = new SimpleQueryParser($request, new Collection());
        $this->assertEquals($sorts, $parser->getSorts());
    }

    /** @test */
    public function it_returns_valid_sorts_when_array()
    {
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('input')->once()
            ->with('sort', '')->andReturn([
                'id',
                '-email',
                'foo',
                'bar',
                'baz',
            ]);

        $sorts = new Collection([
            new Sort('id', 'ASC'),
            new Sort('email', 'DESC'),
            new Sort('foo', 'ASC'),
            new Sort('bar', 'ASC'),
            new Sort('baz', 'ASC'),
        ]);

        $parser = new SimpleQueryParser($request, new Collection());
        $this->assertEquals($sorts, $parser->getSorts());
    }

    /** @test */
    public function it_returns_valid_sorts_when_duplicated_fields_request()
    {
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('input')->once()
            ->with('sort', '')->andReturn('id,-email,foo,bar,baz,id,-id,email');

        $sorts = new Collection([
            new Sort('id', 'DESC'),
            new Sort('email', 'ASC'),
            new Sort('foo', 'ASC'),
            new Sort('bar', 'ASC'),
            new Sort('baz', 'ASC'),
        ]);

        $parser = new SimpleQueryParser($request, new Collection());
        $this->assertEquals($sorts, $parser->getSorts());
    }

    /** @test */
    public function it_returns_valid_sorts_when_array_with_duplicated_fields()
    {
        $request = m::mock('Illuminate\Http\Request');
        $request->shouldReceive('input')->once()
            ->with('sort', '')->andReturn([
                'id',
                '-email',
                'foo',
                'bar',
                'baz',
                'id',
                '-id',
                'email',
            ]);

        $sorts = new Collection([
            new Sort('id', 'DESC'),
            new Sort('email', 'ASC'),
            new Sort('foo', 'ASC'),
            new Sort('bar', 'ASC'),
            new Sort('baz', 'ASC'),
        ]);

        $parser = new SimpleQueryParser($request, new Collection());
        $this->assertEquals($sorts, $parser->getSorts());
    }
}
