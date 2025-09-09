import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useCart } from '../contexts/CartContext';
import QuantityModal from './QuantityModal';
import './Shop.css';

const Shop = () => {
  const [products, setProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('');
  const [priceRange, setPriceRange] = useState({ min: '', max: '' });
  const [categories, setCategories] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const { addToCart } = useCart();

  // Handle opening quantity modal
  const handleAddToCart = (product) => {
    setSelectedProduct(product);
    setIsModalOpen(true);
  };

  // Handle adding product to cart with quantity
  const handleAddToCartWithQuantity = (product, quantity) => {
    addToCart({
      id: product.id,
      name: product.name,
      price: product.price,
      image: product.image,
      description: product.description,
      category: product.category
    }, quantity);
  };

  // Handle closing modal
  const handleCloseModal = () => {
    setIsModalOpen(false);
    setSelectedProduct(null);
  };

  // Fetch products from API
  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoading(true);
        console.log('Fetching products from API...');
        
        const response = await axios.get('http://127.0.0.1:8000/api/products', {
          timeout: 10000, // 10 second timeout
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          }
        });
        
        console.log('API Response:', response);
        
        if (response.data && response.data.success) {
          console.log('Products loaded:', response.data.data);
          setProducts(response.data.data);
          setFilteredProducts(response.data.data);
          
          // Extract unique categories
          const uniqueCategories = [...new Set(response.data.data.map(product => product.category).filter(Boolean))];
          setCategories(uniqueCategories);
        } else {
          console.error('API returned unsuccessful response:', response.data);
          setError('Failed to fetch products');
        }
      } catch (err) {
        console.error('Error fetching products:', err);
        console.error('Error details:', {
          message: err.message,
          code: err.code,
          response: err.response
        });
        setError(`Failed to load products: ${err.message}`);
      } finally {
        setLoading(false);
      }
    };

    fetchProducts();
  }, []);

  // Filter products based on search term, category, and price range
  useEffect(() => {
    let filtered = products;

    // Filter by search term
    if (searchTerm) {
      filtered = filtered.filter(product =>
        product.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        product.description.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }

    // Filter by category
    if (selectedCategory) {
      filtered = filtered.filter(product => product.category === selectedCategory);
    }

    // Filter by price range
    if (priceRange.min !== '') {
      filtered = filtered.filter(product => parseFloat(product.price) >= parseFloat(priceRange.min));
    }
    if (priceRange.max !== '') {
      filtered = filtered.filter(product => parseFloat(product.price) <= parseFloat(priceRange.max));
    }

    setFilteredProducts(filtered);
  }, [products, searchTerm, selectedCategory, priceRange]);

  const handleSearchChange = (e) => {
    setSearchTerm(e.target.value);
  };

  const handleCategoryChange = (e) => {
    setSelectedCategory(e.target.value);
  };

  const handlePriceRangeChange = (e) => {
    const { name, value } = e.target;
    setPriceRange(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const clearFilters = () => {
    setSearchTerm('');
    setSelectedCategory('');
    setPriceRange({ min: '', max: '' });
  };



  if (error) {
    return (
      <div className="shop-container">
        <div className="error-message">
          <h3>Oops! Something went wrong</h3>
          <p>{error}</p>
          <button onClick={() => window.location.reload()} className="retry-btn">
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="shop-container">
      <div className="shop-header">
        <h1>Our Products</h1>
        <p>Discover our amazing collection of handcrafted items</p>
      </div>

      <div className="shop-content">
        {/* Filters Sidebar */}
        <div className="filters-sidebar">
          <div className="filter-section">
            <h3>Search Products</h3>
            <input
              type="text"
              placeholder="Search by name or description..."
              value={searchTerm}
              onChange={handleSearchChange}
              className="search-input"
            />
          </div>

          <div className="filter-section">
            <h3>Category</h3>
            <select
              value={selectedCategory}
              onChange={handleCategoryChange}
              className="category-select"
            >
              <option value="">All Categories</option>
              {categories.map(category => (
                <option key={category} value={category}>
                  {category}
                </option>
              ))}
            </select>
          </div>

          <div className="filter-section">
            <h3>Price Range</h3>
            <div className="price-inputs">
              <input
                type="number"
                name="min"
                placeholder="Min Price"
                value={priceRange.min}
                onChange={handlePriceRangeChange}
                className="price-input"
              />
              <span>to</span>
              <input
                type="number"
                name="max"
                placeholder="Max Price"
                value={priceRange.max}
                onChange={handlePriceRangeChange}
                className="price-input"
              />
            </div>
          </div>

          <button onClick={clearFilters} className="clear-filters-btn">
            Clear All Filters
          </button>
        </div>

        {/* Products Grid */}
        <div className="products-section">
          <div className="products-header">
            <h2>Products ({filteredProducts.length})</h2>
          </div>

          {filteredProducts.length === 0 ? (
            <div className="no-products">
              <h3>No products found</h3>
              <p>Try adjusting your filters or search terms.</p>
            </div>
          ) : (
            <div className="products-grid">
              {filteredProducts.map(product => (
                <div key={product.id} className="product-card">
                  <div className="product-image">
                    {product.image ? (
                      <img
                        src={product.image}
                        alt={product.name}
                        onError={(e) => {
                          e.target.src = '/api/placeholder/300/200';
                        }}
                      />
                    ) : (
                      <div className="placeholder-image">
                        <span>📦</span>
                        <p>No Image</p>
                      </div>
                    )}
                  </div>
                  
                  <div className="product-info">
                    <h3 className="product-name">{product.name}</h3>
                    <p className="product-description">{product.description}</p>
                    
                    {product.category && (
                      <div className="product-category-wrapper">
                        <span className="product-category">{product.category}</span>
                      </div>
                    )}
                    
                    <div className="product-stock-wrapper">
                      <span className="product-stock">
                        {product.stock > 0 ? `${product.stock} in stock` : 'Out of stock'}
                      </span>
                    </div>
                    
                    <div className="product-price-wrapper">
                      <span className="product-price">${parseFloat(product.price).toFixed(2)}</span>
                    </div>
                    
                    <div className="product-button-wrapper">
                      <button 
                        className={`add-to-cart-btn ${product.stock === 0 ? 'disabled' : ''}`}
                        disabled={product.stock === 0}
                        onClick={() => handleAddToCart(product)}
                      >
                        {product.stock === 0 ? 'Out of Stock' : 'Add to Cart'}
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
      
      {/* Quantity Modal */}
      <QuantityModal 
        isOpen={isModalOpen}
        onClose={handleCloseModal}
        product={selectedProduct}
        onAddToCart={handleAddToCartWithQuantity}
      />
    </div>
  );
};

export default Shop;