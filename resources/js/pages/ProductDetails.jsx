import { useState, useEffect, useMemo, useCallback, memo, useRef } from 'react'
import { router, usePage, useForm } from '@inertiajs/react'
import { motion, AnimatePresence } from 'framer-motion'
import { Star, Heart, ShoppingCart, Truck, RefreshCcw, ShieldCheck, ChevronRight, Check, ZoomIn } from 'lucide-react'
import { COLORS, TEXT, BG, SPACING, GRADIENTS, BTN } from '../config/theme'
import { ProductCard } from '../components/common/ProductCard/ProductCard'
import { useDispatch, useSelector } from 'react-redux'
import { addItem } from '../store/cartSlice'
import { toggleWishlist } from '../store/wishlistSlice'
import toast from 'react-hot-toast'
import { SizeGuideModal } from '../components/common/SizeGuideModal'


const IMAGES = [
  'https://images.unsplash.com/photo-1551280857-2b9bbe52acf4?w=800&h=1000&fit=crop&q=80',
  'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&h=1000&fit=crop&q=80',
  'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=800&h=1000&fit=crop&q=80',
]

const SIZES = ['XS', 'S', 'M', 'L', 'XL', '2XL']
const COLOR_OPTIONS = [
  { name: 'Blue', hex: '#1D4ED8' },
  { name: 'Red', hex: '#DC2626' },
  { name: 'Black', hex: '#1e293b' },
  { name: 'White', hex: '#ffffff' },
  { name: 'Green', hex: '#16A34A' },
]

const fadeUp = {
  hidden: { opacity: 0, y: 20 },
  visible: { opacity: 1, y: 0, transition: { duration: 0.5 } }
}

export const ProductDetails = memo(({ id, onNavigate }) => {
  const dispatch = useDispatch()
  const { product: dbProduct, relatedProducts = [], settings = {} } = usePage().props
  const currentId = id || window.location.pathname.split('/').pop()
  const wishlistItems = useSelector((state) => state.wishlist.items)

  const isWished = useMemo(() => {
    return wishlistItems.some(item => (Number(item.id) || item.id) === (Number(currentId) || currentId))
  }, [wishlistItems, currentId])

  const activeProduct = useMemo(() => dbProduct || {}, [dbProduct])

  const showReviews = useMemo(() => settings?.show_reviews !== false, [settings])
  const showPrice = useMemo(() => settings?.show_prices_global !== false && activeProduct?.show_price !== false, [settings, activeProduct])
  const productImages = useMemo(() => activeProduct?.images || [], [activeProduct?.images])
  const productSizes = useMemo(() => activeProduct?.sizes || [], [activeProduct?.sizes])

  const productColors = useMemo(() => {
    return (activeProduct?.colors || []).map(c => {
      if (typeof c === 'string') {
        return { name: c, hex: c };
      }
      return c;
    });
  }, [activeProduct?.colors])

  const [activeImage, setActiveImage] = useState(0)
  const [selectedSize, setSelectedSize] = useState(productSizes[0] || 'M')
  const [selectedColor, setSelectedColor] = useState(productColors[0]?.name || 'Blue')
  const [qty, setQty] = useState(1)
  const [customName, setCustomName] = useState('')
  const [customNumber, setCustomNumber] = useState('')
  const [isSizeGuideOpen, setIsSizeGuideOpen] = useState(false)


  const [zoomScale, setZoomScale] = useState(1.2)
  const [zoomPos, setZoomPos] = useState({ x: 50, y: 50 })
  const [isHovered, setIsHovered] = useState(false)
  const imageContainerRef = useRef(null)

  const handleMouseMove = useCallback((e) => {
    const { left, top, width, height } = e.currentTarget.getBoundingClientRect()
    const x = ((e.clientX - left) / width) * 100
    const y = ((e.clientY - top) / height) * 100
    setZoomPos({ x, y })
  }, [])

  const handleMouseEnter = useCallback(() => {
    setIsHovered(true)
  }, [])

  const handleMouseLeave = useCallback(() => {
    setIsHovered(false)
    setZoomScale(1.2)
    setZoomPos({ x: 50, y: 50 })
  }, [])

  useEffect(() => {
    const container = imageContainerRef.current
    if (!container) return

    const handleWheelEvent = (e) => {
      e.preventDefault()
      setZoomScale(prev => {
        const change = e.deltaY < 0 ? 0.05 : -0.05
        const newScale = prev + change
        return Math.min(Math.max(newScale, 1.0), 3.0)
      })
    }

    container.addEventListener('wheel', handleWheelEvent, { passive: false })
    return () => {
      container.removeEventListener('wheel', handleWheelEvent)
    }
  }, [])

  const reviewForm = useForm({
    reviewer_name: '',
    rating: 5,
    comment: ''
  })

  const handleReviewSubmit = useCallback((e) => {
    e.preventDefault();
    reviewForm.post(`/product-details/${activeProduct.id}/reviews`, {
      preserveScroll: true,
      onSuccess: () => {
        reviewForm.reset();
        toast.success('Thank you! Your review has been submitted.', { icon: '⭐️' });
      }
    });
  }, [reviewForm, activeProduct])

  // Sync state values with props updates
  useEffect(() => {
    if (productSizes.length > 0 && !productSizes.includes(selectedSize)) {
      setSelectedSize(productSizes[0])
    }
  }, [productSizes])

  useEffect(() => {
    if (productColors.length > 0 && !productColors.some(c => c.name === selectedColor)) {
      setSelectedColor(productColors[0]?.name || 'Blue')
    }
  }, [productColors])

  const handleAddToCart = useCallback(() => {
    dispatch(addItem({
      id: activeProduct?.id,
      name: activeProduct?.name || 'Product',
      image: productImages[0] || '',
      price: activeProduct?.price || 0,
      qty: qty,
      size: selectedSize,
      color: selectedColor,
      customName: customName.trim(),
      customNumber: customNumber.trim(),
      show_price: showPrice
    }))
    toast.success(`${activeProduct?.name || 'Product'} has been added to your cart!`, {
      icon: '🛒',
    })
  }, [dispatch, activeProduct, productImages, qty, selectedSize, selectedColor, customName, customNumber, showPrice])

  const handleToggleWishlist = useCallback(() => {
    dispatch(toggleWishlist(activeProduct))
    if (isWished) {
      toast.success('Removed from wishlist', { icon: '💔' })
    } else {
      toast.success(`${activeProduct?.name || 'Product'} saved to wishlist!`, { icon: '❤️' })
    }
  }, [dispatch, activeProduct, isWished])

  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      className={`pt-28 pb-20 min-h-screen ${BG.section}`}
    >
      <div className={SPACING.container}>

        {/* Breadcrumbs */}
        <motion.div
          initial={{ opacity: 0, x: -20 }}
          animate={{ opacity: 1, x: 0 }}
          className="flex items-center gap-2 text-sm text-slate-400 mb-8"
        >
          <button onClick={() => router.visit('/')} className="hover:text-indigo-600 transition-colors">Home</button>
          <ChevronRight size={14} />
          <button onClick={() => router.visit('/products')} className="hover:text-indigo-600 transition-colors">Products</button>
          <ChevronRight size={14} />
          <span className={TEXT.dark}>{activeProduct?.name || (currentId ? currentId.replace(/-/g, ' ').toUpperCase() : '')}</span>
        </motion.div>

        <div className="flex flex-col lg:flex-row gap-12 lg:gap-16 items-start">

          {/* ─── LEFT: Images ──────────────────────── */}
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.5 }}
            className="w-full lg:w-1/2 flex flex-col gap-4 lg:sticky lg:top-28"
          >
            <div
              ref={imageContainerRef}
              onMouseMove={handleMouseMove}
              onMouseEnter={handleMouseEnter}
              onMouseLeave={handleMouseLeave}
              className="w-full aspect-square rounded-2xl overflow-hidden bg-white shadow-lg shadow-indigo-100/50 border border-slate-100 group relative cursor-zoom-in p-6 flex items-center justify-center"
            >
              {isHovered && (
                <div className="absolute top-4 right-4 bg-slate-950/80 backdrop-blur-md text-white text-xs font-semibold px-3 py-1.5 rounded-full flex items-center gap-1.5 pointer-events-none shadow-lg border border-white/10 transition-all duration-300 z-10">
                  <ZoomIn size={14} className="text-indigo-400" />
                  <span>Zoom: {zoomScale.toFixed(1)}x</span>
                  <span className="text-[10px] text-slate-400 border-l border-slate-700 pl-1.5">Scroll to adjust</span>
                </div>
              )}
              <AnimatePresence mode="wait">
                <motion.img
                  key={activeImage}
                  initial={{ opacity: 0, filter: 'blur(10px)', scale: 1 }}
                  animate={{
                    opacity: 1,
                    filter: 'blur(0px)',
                    scale: isHovered ? zoomScale : 1
                  }}
                  exit={{ opacity: 0 }}
                  transition={{
                    opacity: { duration: 0.3 },
                    filter: { duration: 0.3 },
                    scale: { type: 'tween', ease: 'easeOut', duration: 0.25 }
                  }}
                  src={productImages[activeImage] || productImages[0]}
                  alt="Product"
                  className="w-full h-full object-contain"
                  style={{
                    transformOrigin: `${zoomPos.x}% ${zoomPos.y}%`
                  }}
                />
              </AnimatePresence>
            </div>
            {/* Thumbnails */}
            <div className={`grid gap-3 ${productImages.length > 4 ? 'grid-cols-5' : productImages.length > 3 ? 'grid-cols-4' : 'grid-cols-3'}`} style={{ maxHeight: '200px', overflowY: productImages.length > 10 ? 'auto' : 'visible' }}>
              {productImages.map((img, idx) => (
                <button
                  key={idx}
                  onClick={() => setActiveImage(idx)}
                  className={`aspect-square rounded-xl overflow-hidden border-2 transition-all duration-300 bg-slate-50/50 p-2 flex items-center justify-center ${activeImage === idx ? 'border-indigo-600 shadow-lg ring-4 ring-indigo-50' : 'border-transparent opacity-60 hover:opacity-100'}`}
                >
                  <img src={img} alt={`Thumbnail ${idx}`} className="w-full h-full object-contain" />
                </button>
              ))}
            </div>
          </motion.div>

          {/* ─── RIGHT: Details ────────────────────── */}
          <motion.div
            initial="hidden"
            animate="visible"
            variants={{
              visible: { transition: { staggerChildren: 0.1 } }
            }}
            className="w-full lg:w-1/2 flex flex-col"
          >
            <motion.h1 variants={fadeUp} className={`text-4xl md:text-5xl ${TEXT.dark} mb-4 tracking-tight`}>
              {activeProduct?.name}
            </motion.h1>

            {showReviews && (
              <motion.div variants={fadeUp} className="flex items-center gap-3 mb-8">
                <div className="flex text-amber-400">
                  {[1, 2, 3, 4, 5].map(s => <Star key={s} size={18} fill="currentColor" className={s <= Math.round(activeProduct?.rating || 0) ? 'text-amber-400 fill-amber-400' : 'text-slate-200 fill-slate-200'} />)}
                </div>
                <span className={TEXT.mid}>{activeProduct?.rating || 0} ({activeProduct?.reviews || 0} reviews)</span>
              </motion.div>
            )}

            {/* Price */}
            {showPrice && (
              <motion.div variants={fadeUp} className="text-4xl text-indigo-600 mb-6 tracking-tight">${activeProduct?.price || 0}</motion.div>
            )}

            {/* Description */}
            <motion.p variants={fadeUp} className={`${TEXT.mid} leading-relaxed mb-8 text-lg`}>
              {activeProduct?.description}
            </motion.p>

            {/* Select Size */}
            <motion.div variants={fadeUp} className="mb-8">
              <div className="flex items-center justify-between mb-3">
                <h3 className={`${TEXT.dark} font-medium`}>Select Size</h3>
                <button
                  type="button"
                  onClick={() => setIsSizeGuideOpen(true)}
                  className="text-indigo-600 text-sm hover:underline font-semibold"
                >
                  Size Guide
                </button>
              </div>
              <div className="flex flex-wrap gap-2.5">
                {productSizes?.map(sz => (
                  <button
                    key={sz}
                    onClick={() => setSelectedSize(sz)}
                    className={`w-14 h-12 rounded-xl border flex items-center justify-center transition-all duration-300 ${selectedSize === sz
                      ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-200/50 scale-105'
                      : 'bg-white border-slate-200 text-slate-600 hover:border-indigo-300 hover:bg-slate-50'
                      }`}
                  >
                    {sz}
                  </button>
                ))}
              </div>
            </motion.div>

            {/* Select Color */}
            <motion.div variants={fadeUp} className="mb-8">
              <h3 className={`${TEXT.dark} font-medium mb-3`}>Select Color <span className="text-slate-400 font-normal ml-1">({selectedColor})</span></h3>
              <div className="flex flex-wrap gap-3">
                {productColors?.map(c => (
                  <button
                    key={c.name}
                    onClick={() => setSelectedColor(c.name)}
                    className={`relative w-12 h-12 rounded-full border-2 transition-all duration-300 flex items-center justify-center ${selectedColor === c.name
                      ? 'border-indigo-600 scale-110 shadow-md'
                      : 'border-transparent hover:scale-110 shadow-sm'
                      }`}
                    style={{ backgroundColor: c.hex === '#ffffff' ? '#F8fafc' : c.hex }}
                    title={c.name}
                  >
                    {c.hex === '#ffffff' && <span className="absolute inset-0 border border-gray-200 rounded-full" />}
                    {selectedColor === c.name && (
                      <Check size={16} className={c.hex === '#ffffff' ? 'text-slate-800' : 'text-white'} />
                    )}
                  </button>
                ))}
              </div>
            </motion.div>

            {/* Quantity */}
            <motion.div variants={fadeUp} className="mb-10">
              <h3 className={`${TEXT.dark} font-medium mb-3`}>Quantity</h3>
              <div className="inline-flex items-center bg-white border border-slate-200 rounded-xl p-1.5 shadow-sm">
                <button onClick={() => setQty(Math.max(1, qty - 1))} className="w-10 h-10 flex items-center justify-center text-slate-500 hover:bg-slate-50 rounded-lg transition-colors">-</button>
                <span className={`w-14 text-center ${TEXT.dark} font-medium text-lg`}>{qty}</span>
                <button onClick={() => setQty(qty + 1)} className="w-10 h-10 flex items-center justify-center text-slate-500 hover:bg-slate-50 rounded-lg transition-colors">+</button>
              </div>
            </motion.div>

            {/* Personalization Section */}
            <motion.div variants={fadeUp} className="mb-10 p-6 bg-slate-50 rounded-2xl border border-slate-100">
              <div className="flex items-center gap-2 mb-4">
                <ShieldCheck size={18} className="text-indigo-600" />
                <h3 className={`${TEXT.dark} font-semibold`}>Personalize Your Jersey</h3>
              </div>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1.5">Name on Back</label>
                  <input
                    type="text"
                    value={customName}
                    onChange={e => setCustomName(e.target.value)}
                    placeholder="Enter Name"
                    className="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm"
                  />
                </div>
                <div>
                  <label className="block text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1.5">Number</label>
                  <input
                    type="text"
                    maxLength={2}
                    value={customNumber}
                    onChange={e => setCustomNumber(e.target.value)}
                    placeholder="00"
                    className="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm"
                  />
                </div>
              </div>
              <p className="text-[11px] text-slate-400 mt-3 flex items-start gap-1.5">
                <span className="text-amber-500 mt-0.5">●</span> Customization may add 2-3 business days to shipping time.
              </p>
            </motion.div>

            {/* Action Buttons */}
            <motion.div variants={fadeUp} className="flex flex-col sm:flex-row items-center gap-4 mb-6">
              <button
                onClick={handleAddToCart}
                className={`${BTN.primary} flex-1 w-full !py-4 rounded-xl shadow-xl shadow-indigo-200/50 flex items-center justify-center gap-2 text-[15px]`}
              >
                <ShoppingCart size={20} />
                Add to Cart
              </button>
              <button
                onClick={handleToggleWishlist}
                className={`w-full sm:w-16 h-14 rounded-xl border flex items-center justify-center transition-all shadow-sm hover:shadow-md ${isWished
                  ? 'bg-red-50 border-red-200 text-red-500'
                  : 'bg-white border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-200'
                  }`}
              >
                <Heart size={22} className={isWished ? 'fill-red-500 text-red-500' : ''} />
              </button>
            </motion.div>

            <motion.button
              variants={fadeUp}
              onClick={() => router.visit(`/builder?product_id=${dbProduct?.id || activeProduct?.id || ''}`)}
              className={`${BTN.outline} w-full !py-4 rounded-xl flex items-center justify-center gap-2 hover:border-indigo-200 transition-colors`}
            >
              <span className="text-indigo-500"><ShieldCheck size={20} /></span> Customize This Product
            </motion.button>

            {/* Info Cards */}
            <motion.div variants={fadeUp} className="grid grid-cols-3 gap-4 mt-12 mb-12">
              {[
                { icon: Truck, title: 'Free Shipping', desc: 'On orders over $100' },
                { icon: RefreshCcw, title: 'Easy Returns', desc: '30-day return policy' },
                { icon: ShieldCheck, title: 'Quality Guarantee', desc: 'Premium materials' },
              ].map((info, i) => (
                <div key={i} className="bg-white border border-slate-100 rounded-xl p-4 text-center shadow-sm">
                  <info.icon size={24} className="mx-auto mb-2 text-indigo-600" />
                  <h4 className={`${TEXT.dark} text-sm font-medium mb-1`}>{info.title}</h4>
                  <p className="text-[11px] text-slate-400">{info.desc}</p>
                </div>
              ))}
            </motion.div>

            {/* Product Features */}
            <motion.div variants={fadeUp}>
              <h3 className={`${TEXT.dark} font-medium mb-5 text-lg border-b border-slate-100 pb-3`}>Product Features</h3>
              <ul className="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                {(activeProduct?.features || []).map((feature, i) => (
                  <li key={i} className="flex items-start gap-3">
                    <div className="w-1.5 h-1.5 rounded-full bg-indigo-600 mt-2 flex-shrink-0 shadow-sm" />
                    <span className="text-slate-600 leading-relaxed">{feature}</span>
                  </li>
                ))}
              </ul>
            </motion.div>

          </motion.div>
        </div>

        {/* ─── PRODUCT REVIEWS SECTION ───────────────── */}
        {showReviews && (
          <div className="mt-32 pt-16 border-t border-slate-100">
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">

              {/* Rating Summary Card */}
              <div className="bg-white border border-slate-100 rounded-3xl p-8 shadow-xl shadow-slate-200/20">
                <h3 className={`${TEXT.dark} text-2xl font-semibold mb-6`}>Customer Reviews</h3>
                <div className="flex items-baseline gap-4 mb-4">
                  <span className="text-6xl font-bold text-slate-800 tracking-tight">{activeProduct?.rating || 0}</span>
                  <span className="text-lg text-slate-400">out of 5</span>
                </div>
                <div className="flex text-amber-400 mb-6">
                  {[1, 2, 3, 4, 5].map(s => (
                    <Star key={s} size={22} fill="currentColor" className={s <= Math.round(activeProduct?.rating || 0) ? 'text-amber-400 fill-amber-400' : 'text-slate-200 fill-slate-200'} />
                  ))}
                </div>
                <p className="text-sm text-slate-500 mb-6">{activeProduct?.reviews || 0} customer ratings</p>

                {/* Rating bars */}
                <div className="space-y-3.5">
                  {[5, 4, 3, 2, 1].map((stars) => {
                    const count = (activeProduct?.reviews_list || []).filter(r => r.rating === stars).length;
                    const total = (activeProduct?.reviews_list || []).length || 1;
                    const percentage = Math.round((count / total) * 100);
                    return (
                      <div key={stars} className="flex items-center gap-3 text-sm">
                        <span className="w-3 text-slate-500 font-medium">{stars}</span>
                        <Star size={14} className="text-amber-400 fill-amber-400 flex-shrink-0" />
                        <div className="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                          <div className="h-full bg-amber-400 rounded-full" style={{ width: `${percentage}%` }} />
                        </div>
                        <span className="w-8 text-right text-slate-400 text-xs">{percentage}%</span>
                      </div>
                    );
                  })}
                </div>
              </div>

              {/* Review List & Submission form */}
              <div className="lg:col-span-2 space-y-12">
                {/* Review form */}
                <div className="bg-white border border-slate-100 rounded-3xl p-8 shadow-xl shadow-slate-200/20">
                  <h4 className={`${TEXT.dark} text-xl font-semibold mb-6`}>Write a Review</h4>
                  <form onSubmit={handleReviewSubmit} className="space-y-6">
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                      <div>
                        <label className="block text-xs uppercase tracking-wider font-bold text-slate-400 mb-2">Your Name</label>
                        <input
                          type="text"
                          required
                          value={reviewForm.data.reviewer_name}
                          onChange={e => reviewForm.setData('reviewer_name', e.target.value)}
                          placeholder="John Doe"
                          className="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-700"
                        />
                        {reviewForm.errors.reviewer_name && <p className="text-red-500 text-xs mt-1">{reviewForm.errors.reviewer_name}</p>}
                      </div>
                      <div>
                        <label className="block text-xs uppercase tracking-wider font-bold text-slate-400 mb-2">Overall Rating</label>
                        <div className="flex gap-1.5 py-2.5">
                          {[1, 2, 3, 4, 5].map((s) => (
                            <button
                              type="button"
                              key={s}
                              onClick={() => reviewForm.setData('rating', s)}
                              className="text-slate-300 hover:scale-110 transition-transform"
                            >
                              <Star
                                size={28}
                                className={s <= reviewForm.data.rating ? 'text-amber-400 fill-amber-400' : 'text-slate-200 fill-slate-200'}
                              />
                            </button>
                          ))}
                        </div>
                        {reviewForm.errors.rating && <p className="text-red-500 text-xs mt-1">{reviewForm.errors.rating}</p>}
                      </div>
                    </div>

                    <div>
                      <label className="block text-xs uppercase tracking-wider font-bold text-slate-400 mb-2">Comments</label>
                      <textarea
                        rows={4}
                        value={reviewForm.data.comment}
                        onChange={e => reviewForm.setData('comment', e.target.value)}
                        placeholder="What did you like or dislike? How was the fitting?"
                        className="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm text-slate-700 resize-none"
                      />
                      {reviewForm.errors.comment && <p className="text-red-500 text-xs mt-1">{reviewForm.errors.comment}</p>}
                    </div>

                    <button
                      type="submit"
                      disabled={reviewForm.processing}
                      className="px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-semibold text-sm shadow-lg shadow-indigo-200/50 hover:shadow-xl transition-all disabled:opacity-50"
                    >
                      {reviewForm.processing ? 'Submitting...' : 'Submit Review'}
                    </button>
                  </form>
                </div>

                {/* Review list */}
                <div className="space-y-6">
                  <h4 className={`${TEXT.dark} text-xl font-semibold mb-6`}>Recent Feedback</h4>
                  {(activeProduct?.reviews_list || []).length === 0 ? (
                    <div className="text-center py-12 bg-white rounded-3xl border border-slate-100 text-slate-400">
                      No reviews yet. Be the first to rate this product!
                    </div>
                  ) : (
                    <div className="space-y-6">
                      {(activeProduct?.reviews_list || []).map((review) => (
                        <div key={review.id} className="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex gap-4 items-start">
                          {/* Avatar */}
                          <div className="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-lg flex-shrink-0">
                            {review.reviewer_name.charAt(0).toUpperCase()}
                          </div>
                          {/* Content */}
                          <div className="flex-1">
                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                              <h5 className="font-semibold text-slate-700">{review.reviewer_name}</h5>
                              <span className="text-xs text-slate-400">{review.date}</span>
                            </div>
                            <div className="flex text-amber-400 gap-0.5 mb-3">
                              {[1, 2, 3, 4, 5].map(s => (
                                <Star key={s} size={14} fill="currentColor" className={s <= review.rating ? 'text-amber-400 fill-amber-400' : 'text-slate-100 fill-slate-100'} />
                              ))}
                            </div>
                            <p className="text-slate-600 text-sm leading-relaxed whitespace-pre-wrap">{review.comment}</p>
                          </div>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              </div>

            </div>
          </div>
        )}

        {/* ─── RELATED PRODUCTS ──────────────────────── */}
        {relatedProducts.length > 0 && (
          <div className="mt-32 pt-16 border-t border-slate-100">
            <div className="text-center mb-12">
              <h2 className="text-3xl text-slate-800 mb-2 tracking-tight">You Might Also Like</h2>
              <p className="text-slate-500">Customers who viewed this item also viewed these</p>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {relatedProducts.map((product, i) => (
                <ProductCard
                  key={product.id}
                  product={product}
                  index={i}
                  view="grid"
                  onNavigate={onNavigate}
                />
              ))}
            </div>
          </div>
        )}

        {/* Size Guide Modal */}
        <SizeGuideModal isOpen={isSizeGuideOpen} onClose={() => setIsSizeGuideOpen(false)} />

      </div>
    </motion.div>
  )
})
