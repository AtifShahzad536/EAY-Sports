import { useState } from 'react'
import { Link, usePage } from '@inertiajs/react'
import { Mail, Phone, MapPin } from 'lucide-react'
import { FaFacebookF, FaTwitter, FaInstagram, FaLinkedinIn } from 'react-icons/fa'
import { COLORS, TEXT, BTN, SPACING } from '../../../config/theme'
import toast from 'react-hot-toast'
import { useDispatch, useSelector } from 'react-redux'
import { subscribeToNewsletter } from '../../../store/subscriberSlice'

export const Footer = () => {
  const { settings } = usePage().props
  const dispatch = useDispatch()
  const [email, setEmail] = useState('')
  const { loading } = useSelector((state) => state.subscriber)

  // Fallbacks
  const footerEmail = settings?.footer_email || 'info@eaysports.com'
  const footerPhone = settings?.footer_phone || '+1 (555) 123-4567'
  const footerAddress = settings?.footer_address || '123 Sports Avenue, NY 10001'
  const footerDescription = settings?.footer_description || 'Your premier destination for custom sportswear. We deliver quality, performance, and style to athletes and teams worldwide.'

  const socialFacebook = settings?.social_facebook || 'https://facebook.com'
  const socialTwitter = settings?.social_twitter || 'https://twitter.com'
  const socialInstagram = settings?.social_instagram || 'https://instagram.com'
  const socialLinkedin = settings?.social_linkedin || 'https://linkedin.com'

  const companyLinks = settings?.company_links || [
    { label: 'About Us', href: '/about' },
    { label: 'Contact', href: '/contact' },
    { label: 'Find Dealer', href: '/dealer-locator' },
    { label: 'FAQ', href: '/faq' }
  ]

  const productsLinks = settings?.products_links || [
    { label: 'Custom Sportswear', href: '/products' },
    { label: 'Custom Builder', href: '/builder' },
    { label: 'Bulk Orders', href: '/products' },
    { label: 'Privacy Policy', href: '/privacy-policy' }
  ]

  const supportLinks = settings?.support_links || [
    { label: 'Shipping Info', href: '/faq' },
    { label: 'Returns', href: '/faq' },
    { label: 'Size Guide', href: '/faq' },
    { label: 'Terms of Service', href: '/terms-of-service' }
  ]

  const handleSubscribe = (e) => {
    e.preventDefault()
    
    if (!email) {
      toast.error('Please enter your email address.')
      return
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailPattern.test(email)) {
      toast.error('Please enter a valid email address.')
      return
    }

    const toastId = toast.loading('Subscribing you to our newsletter...')
    
    dispatch(subscribeToNewsletter(email))
      .unwrap()
      .then((data) => {
        if (data.success) {
          toast.success(data.message || 'Subscribed successfully!', { id: toastId, icon: '🎉' })
          setEmail('')
        } else {
          toast.error(data.message || 'Subscription failed.', { id: toastId })
        }
      })
      .catch((err) => {
        toast.error(err || 'An error occurred. Please try again.', { id: toastId })
      })
  }


  return (
    <footer className="bg-white pt-20 pb-10 border-t border-gray-100">
      <div className={`${SPACING.container}`}>
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-20">

          {/* Brand & Contact */}
          <div className="lg:col-span-5">
            <div className="flex items-center gap-3 mb-6">
              <div className="w-12 h-12 rounded-xl bg-transparent flex items-center justify-center overflow-hidden">
                <img src="/eay-logo.png" alt="Logo" className="w-full h-full object-contain" />
              </div>
              <h2 className={`text-3xl font-bold tracking-tight text-slate-800`}>EAY SPORTS</h2>
            </div>
            <p className="text-slate-500 text-[16px] leading-snug mb-10 max-w-sm">
              {footerDescription}
            </p>
            <div className="space-y-4">
              {[
                { icon: <Mail size={20} />,   bg: 'bg-indigo-700 shadow-zinc-200/50',  text: footerEmail },
                { icon: <Phone size={20} />,  bg: 'bg-indigo-600 shadow-zinc-200/50', text: footerPhone },
                { icon: <MapPin size={20} />, bg: 'bg-indigo-500 shadow-zinc-200/50',  text: footerAddress },
              ].map((item, i) => (
                <div key={i} className="flex items-center gap-4">
                  <div className={`w-12 h-12 rounded-full ${item.bg} flex items-center justify-center text-white shadow-lg`}>
                    {item.icon}
                  </div>
                  <span className="text-slate-600 font-medium">{item.text}</span>
                </div>
              ))}
            </div>
          </div>

          {/* Links */}
          <div className="lg:col-span-7 grid grid-cols-2 sm:grid-cols-3 gap-8 sm:gap-12">
            {[
              { title: 'Company',  links: companyLinks },
              { title: 'Products', links: productsLinks },
              { title: 'Support',  links: supportLinks },
            ].map(col => (
              <div key={col.title}>
                <h4 className="text-sm uppercase tracking-widest font-bold text-slate-400 mb-6">{col.title}</h4>
                <ul className="space-y-3.5">
                  {col.links && col.links.map(item => (
                    <li key={item.label}>
                      {item.href && item.href.startsWith('/') ? (
                        <Link 
                          href={item.href}
                          className="text-slate-600 hover:text-indigo-600 transition-colors text-[15px] text-left hover:translate-x-1 duration-200 transform block"
                        >
                          {item.label}
                        </Link>
                      ) : (
                        <a 
                          href={item.href || '#'}
                          className="text-slate-600 hover:text-indigo-600 transition-colors text-[15px] text-left hover:translate-x-1 duration-200 transform block"
                        >
                          {item.label}
                        </a>
                      )}
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>

        {/* Bottom: Socials + Newsletter */}
        <div className="flex flex-col lg:flex-row items-center justify-between pt-10 border-t border-gray-100 gap-8">
          <div className="flex items-center gap-4">
            {/* Social Icons — slightly smaller on mobile */}
            {[
              { icon: <FaFacebookF />, bg: 'bg-indigo-700', href: socialFacebook },
              { icon: <FaTwitter />,   bg: 'bg-indigo-600', href: socialTwitter },
              { icon: <FaInstagram />, bg: 'bg-indigo-500', href: socialInstagram },
              { icon: <FaLinkedinIn />,bg: 'bg-indigo-400', href: socialLinkedin },
            ].filter(s => s.href).map((s, i) => (
              <a 
                key={i} 
                href={s.href} 
                target="_blank" 
                rel="noopener noreferrer" 
                className={`w-9 h-9 sm:w-11 sm:h-11 rounded-lg ${s.bg} text-white flex items-center justify-center hover:scale-110 hover:bg-indigo-500 transition-all shadow-lg [&>svg]:w-4 sm:[&>svg]:w-[18px] cursor-pointer`}
              >
                {s.icon}
              </a>
            ))}
          </div>
          <form onSubmit={handleSubscribe} className="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full max-w-md px-4 sm:px-0">
            <input
              type="email"
              placeholder="Enter your email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              disabled={loading}
              className="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 sm:px-6 sm:py-4 text-xs sm:text-base text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all disabled:opacity-60"
            />
            <button 
              type="submit"
              disabled={loading}
              className={`${BTN.primary} !px-5 !py-2.5 sm:!px-8 sm:!py-4 rounded-lg text-xs sm:text-base shadow-xl shadow-blue-100 hover:scale-105 transition-transform disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer`}
            >
              {loading ? 'Subscribing...' : 'Subscribe'}
            </button>
          </form>
        </div>

        <div className="mt-10 text-center text-gray-400 text-sm">
          <p>© 2026 EAY SPORTS. Excellence in Sportswear.</p>
        </div>
      </div>
    </footer>
  )
}

