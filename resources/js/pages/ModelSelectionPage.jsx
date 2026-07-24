import React from 'react';
import { router } from '@inertiajs/react';
import DesignPreview from '../features/builder/DesignPreview';
import { Pagination } from '../components/common/Pagination/Pagination';

export default function ModelSelectionPage({ categoryName, models = [], pagination }) {
  const handleSelectModel = (modelId) => {
    router.visit(`/builder/${modelId}`);
  };

  const currentPage = pagination?.current_page || 1;
  const totalPages = pagination?.last_page || 1;

  const handlePageChange = (page) => {
    const params = new URLSearchParams(window.location.search);
    params.set('page', page);
    router.visit(`${window.location.pathname}?${params.toString()}`, {
      preserveState: true,
      preserveScroll: true,
      replace: true
    });
  };

  return (
    <div className="w-full min-h-screen bg-slate-50 font-['Outfit'] text-slate-900 py-12 px-6 lg:px-12">
      <div className="max-w-7xl mx-auto flex flex-col gap-10">
        
        {/* Top Header */}
        <div className="flex items-center justify-between border-b border-slate-200 pb-6">
          <div className="flex items-center gap-4">
            <button
              onClick={() => router.visit('/builder')}
              className="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-100 transition shadow-sm"
            >
              ← Back to Basis
            </button>
            <div>
              <span className="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Model Variations</span>
              <h1 className="text-2xl lg:text-3xl font-black text-slate-900 uppercase tracking-tight">
                {categoryName ? `${categoryName} Variations` : 'All Kit Models'}
              </h1>
            </div>
          </div>
          <span className="px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold uppercase tracking-widest">
            {pagination?.total ?? models.length} Models Found
          </span>
        </div>

        {/* Models Grid */}
        {models.length === 0 ? (
          <div className="flex flex-col items-center justify-center py-32 bg-white border border-slate-200 rounded-2xl">
            <p className="text-sm font-bold text-slate-400 uppercase tracking-widest">No models found for "{categoryName}".</p>
            <button
              onClick={() => router.visit('/builder')}
              className="mt-4 px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-indigo-700 transition"
            >
              Browse All Basis Models
            </button>
          </div>
        ) : (
          <>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-8">
              {models.map((model) => (
                <div
                  key={model.id}
                  onClick={() => handleSelectModel(model.id)}
                  className="group flex flex-col gap-3 bg-white p-4 rounded-2xl border border-slate-200 hover:border-indigo-500/40 hover:shadow-xl transition-all duration-300 cursor-pointer"
                >
                  <div className="aspect-[4/5] relative bg-slate-50 rounded-xl overflow-hidden border border-slate-100 flex items-center justify-center">
                    <DesignPreview
                      modelUrl={model.modelUrl}
                      mapping={model.mapping}
                      primaryColor="#ffffff"
                      secondaryColor="#ffffff"
                      thirdColor="#ffffff"
                      layersMetadata={model.layers_metadata || {}}
                    />
                    <div className="absolute top-3 left-3 px-2 py-0.5 bg-white/90 rounded text-[9px] font-black text-slate-700 uppercase">
                      {model.id}
                    </div>
                  </div>

                  <div className="flex flex-col gap-1 px-1">
                    <h3 className="text-sm font-extrabold text-slate-800 uppercase group-hover:text-indigo-600 transition-colors">
                      {model.name}
                    </h3>
                    <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                      Click to open 3D Customizer
                    </span>
                  </div>

                  <button className="w-full mt-2 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition">
                    <span>Open Customizer</span>
                    →
                  </button>
                </div>
              ))}
            </div>

            {pagination && pagination.last_page > 1 && (
              <div className="flex justify-center mt-12 border-t border-slate-200 pt-8">
                <Pagination
                  currentPage={currentPage}
                  totalPages={totalPages}
                  onPageChange={handlePageChange}
                />
              </div>
            )}
          </>
        )}

      </div>
    </div>
  );
}
