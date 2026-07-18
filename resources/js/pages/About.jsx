import { motion } from 'framer-motion'
import * as LucideIcons from 'lucide-react'
import { COLORS, TEXT, BG, SPACING, GRADIENTS, BTN } from '../config/theme'
import { router } from '@inertiajs/react'

const fadeUp = (delay = 0) => ({
  initial: { opacity: 0, y: 30 },
  whileInView: { opacity: 1, y: 0 },
  viewport: { once: true },
  transition: { duration: 0.6, delay },
})

export const About = ({ hero, stats = [], values = [], team = [] }) => {
  // Hero fallbacks
  const heroTitle = hero?.title || 'Built for Athletes,'
  const heroSubtitle = hero?.subtitle || 'By Athletes'
  const heroDescription = hero?.description || 'EAY Sports was founded with a single mission — give every team access to world-class custom sportswear without compromise on quality, price, or speed.'

  return (
    <div className="pt-20 bg-white min-h-screen">

      {/* Hero Banner */}
      <section className="relative bg-indigo-600 py-24 overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-10 left-20 w-64 h-64 rounded-full bg-white blur-3xl" />
          <div className="absolute bottom-0 right-10 w-96 h-96 rounded-full bg-purple-400 blur-3xl" />
        </div>
        <div className={`${SPACING.container} max-w-4xl text-center relative z-10`}>
          <motion.div {...fadeUp()} className="inline-flex items-center gap-2 bg-white/15 border border-white/20 px-4 py-1.5 rounded-full mb-6">
            <LucideIcons.Users size={14} className="text-white" />
            <span className="text-xs text-white uppercase tracking-widest">Our Story</span>
          </motion.div>
          <motion.h1 {...fadeUp(0.1)} className="text-[40px] md:text-[60px] text-white leading-[1.1] mb-6">
            {heroTitle}<br />
            <span className="text-yellow-300">{heroSubtitle}</span>
          </motion.h1>
          <motion.p {...fadeUp(0.2)} className="text-lg text-white/80 max-w-2xl mx-auto leading-relaxed">
            {heroDescription}
          </motion.p>
        </div>
      </section>

      {/* Stats */}
      {stats.length > 0 && (
        <section className="bg-slate-950 py-16">
          <div className={`${SPACING.container} max-w-5xl grid grid-cols-2 md:grid-cols-4 gap-8 text-center`}>
            {stats.map((s, i) => (
              <motion.div key={s.label || i} {...fadeUp(i * 0.1)}>
                <div className="text-[36px] md:text-[48px] text-white mb-1">{s.value}</div>
                <div className="text-sm text-slate-400">{s.label}</div>
              </motion.div>
            ))}
          </div>
        </section>
      )}

      {/* Mission */}
      <section className={`py-24 ${SPACING.container} max-w-6xl`}>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
          <motion.div {...fadeUp()}>
            <span className="text-xs text-indigo-600 uppercase tracking-widest mb-3 block">Our Mission</span>
            <h2 className="text-[32px] md:text-[42px] text-slate-700 leading-tight mb-6">
              Empowering Teams with the Gear They Deserve
            </h2>
            <p className="text-slate-500 text-[16px] leading-relaxed mb-6">
              We believe every team — from grassroots clubs to professional squads — deserves premium sportswear that reflects their identity. Our custom design platform puts the power in your hands.
            </p>
            <p className="text-slate-500 text-[16px] leading-relaxed">
              With cutting-edge printing technology, sustainably sourced fabrics, and a design team that lives and breathes sport, we're not just making jerseys — we're building legacies.
            </p>
          </motion.div>
          <motion.div {...fadeUp(0.2)} className="grid grid-cols-2 gap-4">
            {values.map(({ icon, title, desc }) => {
              const IconComponent = LucideIcons[icon] || LucideIcons.HelpCircle
              return (
                <div key={title} className="bg-slate-50 rounded-2xl p-5 border border-slate-100 hover:border-indigo-600/20 hover:shadow-md transition-all">
                  <div className="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center mb-3">
                    <IconComponent size={20} className="text-indigo-600" />
                  </div>
                  <h3 className="text-slate-700 text-sm mb-1">{title}</h3>
                  <p className="text-slate-500 text-xs leading-relaxed">{desc}</p>
                </div>
              )
            })}
          </motion.div>
        </div>
      </section>

      {/* Team */}
      {team.length > 0 && (
        <section className="py-20 bg-slate-50">
          <div className={`${SPACING.container} max-w-5xl`}>
            <motion.div {...fadeUp()} className="text-center mb-14">
              <span className="text-xs text-indigo-600 uppercase tracking-widest mb-3 block">The People Behind EAY</span>
              <h2 className="text-[32px] md:text-[42px] text-slate-700">Meet Our Team</h2>
            </motion.div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
              {team.map((member, i) => (
                <motion.div key={member.name || i} {...fadeUp(i * 0.1)} className="text-center group">
                  <div className={`w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br ${member.gradient} flex items-center justify-center text-white text-2xl mb-4 shadow-lg group-hover:scale-105 transition-transform`}>
                    {member.initials}
                  </div>
                  <h3 className="text-slate-700 text-sm">{member.name}</h3>
                  <p className="text-slate-500 text-xs mt-1">{member.role}</p>
                </motion.div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* CTA */}
      <section className="py-20 px-4 text-center">
        <motion.div {...fadeUp()} className="max-w-2xl mx-auto">
          <LucideIcons.Globe size={40} className="text-indigo-600 mx-auto mb-5" />
          <h2 className="text-[30px] md:text-[40px] text-slate-700 mb-4">Ready to gear up your team?</h2>
          <p className="text-slate-500 mb-8">Join thousands of teams worldwide who trust EAY Sports for their custom sportswear.</p>
          <button 
            onClick={() => router.visit('/builder')}
            className="inline-flex items-center gap-2 bg-indigo-600 text-white px-8 py-4 rounded-full text-lg hover:bg-indigo-700 hover:scale-105 transition-all shadow-xl shadow-zinc-200"
          >
            Start Designing <LucideIcons.ChevronRight size={20} />
          </button>
        </motion.div>
      </section>

    </div>
  )
}
