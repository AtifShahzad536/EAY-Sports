import React, { useEffect, useState } from 'react'
import { motion, AnimatePresence } from 'framer-motion'
import { X } from 'lucide-react'

export const SizeGuideModal = ({ isOpen, onClose }) => {
  const [tabs, setTabs] = useState([])
  const [activeTab, setActiveTab] = useState('')
  const [loading, setLoading] = useState(false)

  // Prevent background scrolling when open
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden'
      document.documentElement.classList.add('lenis-stopped')
    } else {
      document.body.style.overflow = 'unset'
      document.documentElement.classList.remove('lenis-stopped')
    }
    return () => {
      document.body.style.overflow = 'unset'
      document.documentElement.classList.remove('lenis-stopped')
    }
  }, [isOpen])

  // Handle escape key
  useEffect(() => {
    const handleEscape = (e) => {
      if (e.key === 'Escape') onClose()
    }
    window.addEventListener('keydown', handleEscape)
    return () => window.removeEventListener('keydown', handleEscape)
  }, [onClose])

  // Fetch size charts from database
  useEffect(() => {
    if (isOpen) {
      setLoading(true)
      fetch('/api/size-charts')
        .then(res => res.json())
        .then(data => {
          setTabs(data)
          if (data && data.length > 0) {
            setActiveTab(data[0].id)
          }
          setLoading(false)
        })
        .catch(err => {
          console.error("Failed to load size charts:", err)
          setLoading(false)
        })
    }
  }, [isOpen])

  const activeImage = tabs.find(t => t.id === activeTab)?.image

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-[99999] flex justify-end">
          {/* Backdrop Blur Overlay */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.3 }}
            className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
            onClick={onClose}
          />

          {/* Drawer Container (Slides from Right to Left) */}
          <motion.div
            initial={{ x: '100%' }}
            animate={{ x: 0 }}
            exit={{ x: '100%' }}
            transition={{ type: 'spring', damping: 25, stiffness: 200 }}
            className="relative w-full max-w-lg md:max-w-xl h-full bg-white shadow-2xl flex flex-col z-10 border-l border-slate-100"
          >
            {/* Header */}
            <div className="flex items-center justify-between p-5 border-b border-slate-100">
              <div>
                <h3 className="text-xl font-bold text-slate-800 tracking-tight">Size Chart Guide</h3>
                <p className="text-xs text-slate-400 mt-0.5">Select a category to view the measurements chart.</p>
              </div>
              <button
                onClick={onClose}
                className="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors"
                title="Close"
              >
                <X size={20} />
              </button>
            </div>

            {/* Navigation Tabs */}
            <div className="flex border-b border-slate-100 bg-slate-50/50 p-2 gap-1 overflow-x-auto min-h-[53px]">
              {loading ? (
                <div className="flex gap-2 w-full">
                  <div className="h-8 w-24 bg-slate-200 animate-pulse rounded-xl" />
                  <div className="h-8 w-24 bg-slate-200 animate-pulse rounded-xl" />
                  <div className="h-8 w-24 bg-slate-200 animate-pulse rounded-xl" />
                </div>
              ) : tabs.length > 0 ? (
                tabs.map((tab) => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className={`px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider whitespace-nowrap transition-all duration-200 ${
                      activeTab === tab.id
                        ? 'bg-slate-900 text-white shadow-sm'
                        : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100/50'
                    }`}
                  >
                    {tab.label}
                  </button>
                ))
              ) : (
                <span className="text-xs text-slate-400 p-2">No categories available</span>
              )}
            </div>

            {/* Scrollable Size Chart Content */}
            <div data-lenis-prevent className="flex-1 overflow-y-auto p-5 bg-slate-50/30 flex flex-col items-center justify-start">
              {loading ? (
                <div className="w-full aspect-[4/5] bg-slate-200 animate-pulse rounded-2xl border border-slate-100" />
              ) : activeImage ? (
                /* Image Container with shadow and nice layout */
                <div className="w-full bg-white rounded-2xl border border-slate-100 p-2 shadow-sm">
                  <img
                    src={activeImage}
                    alt={`${activeTab} size chart`}
                    className="w-full h-auto object-contain rounded-xl select-none"
                    style={{ maxHeight: 'calc(100vh - 180px)' }}
                  />
                </div>
              ) : (
                <div className="text-center py-12 text-slate-400">
                  <i className="bi bi-image text-3xl mb-2 d-block"></i>
                  No size chart selected or available.
                </div>
              )}
            </div>

            {/* Footer */}
            <div className="p-4 border-t border-slate-100 bg-white flex justify-end">
              <button
                onClick={onClose}
                className="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs uppercase tracking-wider transition-all"
              >
                Close Drawer
              </button>
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  )
}
