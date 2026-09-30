import ProductList from '@/ts/features/products/list/_components/ProductList'
import PriceRange from '@/ts/features/products/list/_components/PriceRange'

new ProductList('products-wrapper')
new PriceRange('minPrice', 'maxPrice', 'minPrice-output', 'maxPrice-output')
