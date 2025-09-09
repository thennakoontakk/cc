import React, { useState, useEffect } from 'react';
import './Home.css';
import Footer from './Footer';
import ChatBot from './ChatBot';

function Home() {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchProducts();
  }, []);

  const fetchProducts = async () => {
    try {
      const response = await fetch('http://127.0.0.1:8000/api/products');
      const data = await response.json();
      
      if (data.success) {
        setProducts(data.data);
      } else {
        setError('Failed to fetch products');
      }
    } catch (err) {
      setError('Error connecting to server');
      console.error('Error fetching products:', err);
    } finally {
      setLoading(false);
    }
  };

  const getProductsByCategory = (category, limit = 5) => {
    // First try exact match
    let filteredProducts = products.filter(product => 
      product.category && product.category.toLowerCase() === category.toLowerCase()
    );
    
    // If no exact match, try partial match
    if (filteredProducts.length === 0) {
      filteredProducts = products.filter(product => 
        product.category && product.category.toLowerCase().includes(category.toLowerCase())
      );
    }
    
    // If still no match, try keywords
    if (filteredProducts.length === 0) {
      const keywords = category.toLowerCase().split(' ');
      filteredProducts = products.filter(product => {
        const productCategory = product.category ? product.category.toLowerCase() : '';
        const productName = product.name ? product.name.toLowerCase() : '';
        return keywords.some(keyword => 
          productCategory.includes(keyword) || productName.includes(keyword)
        );
      });
    }
    
    return filteredProducts.slice(0, limit);
  };

  const getLatestProducts = () => {
    return products.slice(0, 10);
  };
  return (
    <div className="home-page">
      {/* Hero Section */}
      <section className="hero-section">
        <div className="hero-container">
          <div className="hero-content">
            <h1 className="hero-title">
              <span className="title-line">Your Ideas,</span>
              <span className="title-line highlight">Our Artistry,</span>
              <span className="title-line">Timeless Craft.</span>
            </h1>
            <button className="shop-btn">Shop All Crafts</button>
          </div>
          <div className="hero-image">
            <img src="/hero-image.png" alt="Craft showcase" />
          </div>
        </div>
      </section>

      {/* Main Content Container */}
      <div className="main-content">
        {/* New Arrivals Section */}
        <section className="section new-arrivals">
          <h2 className="section-title">New Arrivals</h2>
          {loading ? (
            <div className="loading">Loading products...</div>
          ) : error ? (
            <div className="error">Error: {error}</div>
          ) : (
            <>
              <div className="products-grid">
                {getLatestProducts().slice(0, 5).map((product) => (
                  <div key={product.id} className="product-card">
                    <div className="product-image">
                    <img 
                      src={product.image || '/api/placeholder/200/200'} 
                      alt={product.name}
                      onError={(e) => {
                        e.target.src = '/api/placeholder/200/200';
                      }}
                    />
                    </div>
                    <div className="product-info">
                      <h3>{product.name}</h3>
                      <p className="price">${product.price}</p>
                      <div className="rating">⭐⭐⭐⭐⭐</div>
                    </div>
                  </div>
                ))}
              </div>
              {getLatestProducts().length > 5 && (
                <div className="products-grid">
                  {getLatestProducts().slice(5, 10).map((product) => (
                    <div key={product.id} className="product-card">
                      <div className="product-image">
                        <img 
                          src={product.image || '/api/placeholder/200/200'} 
                          alt={product.name}
                          onError={(e) => {
                            e.target.src = '/api/placeholder/200/200';
                          }}
                        />
                      </div>
                      <div className="product-info">
                        <h3>{product.name}</h3>
                        <p className="price">${product.price}</p>
                        <div className="rating">⭐⭐⭐⭐⭐</div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </>
          )}
          <button className="view-all-btn">View All</button>
        </section>

        {/* Categories Section */}
        <section className="section categories-section">
          <img src="/category.png" alt="Categories" style={{width: '100%', height: 'auto'}} />
        </section>

        {/* Welcome Wall Hangings Section */}
        <section className="section wall-hangings">
          <h2 className="section-title">Welcome Wall Hangings</h2>
          {loading ? (
            <div className="loading">Loading wall hangings...</div>
          ) : error ? (
            <div className="error">Error loading products</div>
          ) : (
            <div className="products-grid">
              {getProductsByCategory('wall hanging').length > 0 ? (
                getProductsByCategory('wall hanging').map((product) => (
                  <div key={product.id} className="product-card">
                    <div className="product-image">
                      <img 
                        src={product.image || '/api/placeholder/200/200'} 
                        alt={product.name}
                        onError={(e) => {
                          e.target.src = '/api/placeholder/200/200';
                        }}
                      />
                    </div>
                    <div className="product-info">
                      <h3>{product.name}</h3>
                      <p className="price">${product.price}</p>
                      <div className="rating">⭐⭐⭐⭐⭐</div>
                    </div>
                  </div>
                ))
              ) : (
                <div className="no-products">
                  <p>No wall hangings available at the moment.</p>
                  <p>Check out our other amazing products!</p>
                </div>
              )}
            </div>
          )}
          <button className="view-all-btn">View All</button>
        </section>

        {/* Hand Painted Items Section */}
        <section className="section hand-painted">
          <h2 className="section-title">Hand Painted Items</h2>
          {loading ? (
            <div className="loading">Loading hand painted items...</div>
          ) : error ? (
            <div className="error">Error loading products</div>
          ) : (
            <div className="products-grid">
              {getProductsByCategory('hand painted').length > 0 ? (
                getProductsByCategory('hand painted').map((product) => (
                  <div key={product.id} className="product-card">
                    <div className="product-image">
                      <img 
                        src={product.image || '/api/placeholder/200/200'} 
                        alt={product.name}
                        onError={(e) => {
                          e.target.src = '/api/placeholder/200/200';
                        }}
                      />
                    </div>
                    <div className="product-info">
                      <h3>{product.name}</h3>
                      <p className="price">${product.price}</p>
                      <div className="rating">⭐⭐⭐⭐⭐</div>
                    </div>
                  </div>
                ))
              ) : (
                <div className="no-products">
                  <p>No hand painted items available at the moment.</p>
                  <p>Check out our other amazing products below!</p>
                </div>
              )}
            </div>
          )}
          <button className="view-all-btn">View All</button>
        </section>

        {/* Decorative Items Section */}
        <section className="section decorative-items">
          <h2 className="section-title">Decorative Items</h2>
          {loading ? (
            <div className="loading">Loading decorative items...</div>
          ) : error ? (
            <div className="error">Error loading products</div>
          ) : (
            <div className="products-grid">
              {getProductsByCategory('decorative').length > 0 ? (
                getProductsByCategory('decorative').map((product) => (
                  <div key={product.id} className="product-card">
                    <div className="product-image">
                      <img 
                        src={product.image || '/api/placeholder/200/200'} 
                        alt={product.name}
                        onError={(e) => {
                          e.target.src = '/api/placeholder/200/200';
                        }}
                      />
                    </div>
                    <div className="product-info">
                      <h3>{product.name}</h3>
                      <p className="price">${product.price}</p>
                      <div className="rating">⭐⭐⭐⭐⭐</div>
                    </div>
                  </div>
                ))
              ) : (
                <div className="no-products">
                  <p>No decorative items available at the moment.</p>
                </div>
              )}
            </div>
          )}
          <button className="view-all-btn">View All</button>
        </section>

        {/* Handmade Crafts Section */}
        <section className="section handmade-crafts">
          <h2 className="section-title">Handmade Crafts</h2>
          {loading ? (
            <div className="loading">Loading handmade crafts...</div>
          ) : error ? (
            <div className="error">Error loading products</div>
          ) : (
            <div className="products-grid">
              {getProductsByCategory('handmade').length > 0 ? (
                getProductsByCategory('handmade').map((product) => (
                  <div key={product.id} className="product-card">
                    <div className="product-image">
                      <img 
                        src={product.image || '/api/placeholder/200/200'} 
                        alt={product.name}
                        onError={(e) => {
                          e.target.src = '/api/placeholder/200/200';
                        }}
                      />
                    </div>
                    <div className="product-info">
                      <h3>{product.name}</h3>
                      <p className="price">${product.price}</p>
                      <div className="rating">⭐⭐⭐⭐⭐</div>
                    </div>
                  </div>
                ))
              ) : (
                <div className="no-products">
                  <p>No handmade crafts available at the moment.</p>
                </div>
              )}
            </div>
          )}
          <button className="view-all-btn">View All</button>
        </section>

        {/* Ready to Create Section */}
        <section className="section ready-to-create">
          <img src="/ready to create.png" alt="Ready to Create" style={{width: '100%', height: 'auto'}} />
        </section>

        {/* Reviews Section */}
        <section className="section reviews">
          <h2 className="section-title">Reviews</h2>
          <div className="reviews-grid">
            <div className="review-card">
              <div className="reviewer-info">
                <img src="/avatar1.png" alt="Reviewer" />
                <div className="reviewer-details">
                  <h4>Sahan Lakmal</h4>
                  <div className="rating">⭐⭐⭐⭐⭐</div>
                </div>
              </div>
              <p>"Amazing quality crafts! The attention to detail is incredible and the customer service is outstanding."</p>
            </div>
            <div className="review-card">
              <div className="reviewer-info">
                <img src="/avatar2.png" alt="Reviewer" />
                <div className="reviewer-details">
                  <h4>Nathasha Perera</h4>
                  <div className="rating">⭐⭐⭐⭐⭐</div>
                </div>
              </div>
              <p>"Love the custom craft options! They brought my vision to life perfectly."</p>
            </div>
            <div className="review-card">
              <div className="reviewer-info">
                <img src="/avatar3.png" alt="Reviewer" />
                <div className="reviewer-details">
                  <h4>Priyanmi Silva</h4>
                  <div className="rating">⭐⭐⭐⭐⭐</div>
                </div>
              </div>
              <p>"Fast delivery and beautiful packaging. Highly recommend Crafters' Corner!"</p>
            </div>
          </div>
        </section>
      </div>
      
      {/* Footer */}
      <Footer />
      
      {/* Floating ChatBot */}
      <ChatBot />
    </div>
  );
}

export default Home;