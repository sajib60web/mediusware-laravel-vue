@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>

    <div class="card">
        <form action="{{ route('product.index') }}" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" value="{{ request('title') }}" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="">Select Variant</option>
                        @foreach($variants as $variant_val)
                            <optgroup label="{{ $variant_val->title }}">
                                @foreach($variant_val->productVariants as $productVariant)
                                    <option value="{{ $productVariant->variant }}" @if($productVariant->variant == request('variant')) selected @endif>{{ $productVariant->variant }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" value="{{ request('price_from') }}" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" value="{{ request('price_to') }}" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" value="{{ request('date') }}" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th width="20%">Title</th>
                            <th width="30%">Description</th>
                            <th>Variant</th>
                            <th width="150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $product->title }} <br> Created at : {{ $product->created_at->format('d-M-Y') }}</td>
                                <td>{{ $product->description }}</td>
                                <td>
                                    <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">
                                        <dt class="col-sm-3 pb-0">
                                            @if($product->productVariantPrice)
                                                @foreach($product->productVariantPrice as $variant)
                                                    @if($variant->variantNameOne || $variant->variantNameTwo || $variant->variantNameThree)
                                                        {{ @$variant->variantNameOne->variant .' /' }}
                                                        {{ @$variant->variantNameTwo->variant .' /' }}
                                                        {{ @$variant->variantNameThree->variant }}
                                                        <br>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                @if($product->productVariantPrice)
                                                    @foreach($product->productVariantPrice as $variantPrice)
                                                        <dt class="col-sm-6 pb-0">Price : {{ number_format($variantPrice->price) }}</dt>
                                                        <dd class="col-sm-6 pb-0">InStock : {{ number_format($variantPrice->stock) }}</dd>
                                                    @endforeach
                                                @endif
                                            </dl>
                                        </dd>
                                    </dl>
                                    <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} out of {{ $products->total() }}</p>
                </div>
                <div class="col-md-2">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
