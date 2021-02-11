<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mapa</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <header class="px-4 pt-4 bg-white">
            <div class="bg-indigo-100 rounded px-6 py-4 grid grid-cols-2">
              <div class="grid grid-cols-2 gap-x-1">
                <div class="flex items-center">
                  <img src="https://raw.githubusercontent.com/do-community/travellist-laravel-demo/main/public/img/marker_visited.png" alt="">
                  <span class="ml-2">
                    Hospitales CON camas UCI
                  </span>
                </div>

                <div class="flex items-center">
                  <img src="https://raw.githubusercontent.com/do-community/travellist-laravel-demo/main/public/img/marker_togo.png" alt="">
                  <span class="ml-2">
                    Hospitales SIN camas UCI
                  </span>
                </div>
              </div>
            </div>
          </header>

          <section class="p-4 bg-white">
            <div class="h-0 w-full relative overflow-hidden ratio-16/9">
              <div class="w-full h-full absolute top-0 left-0 object-cover rounded" ref="map-root">
                <div class="bg-white shadow-lg absolute bottom-0 left-0 px-3 py-2 rounded w-56" ref="pop-up">
                  <div ref="pop-up__info" class="w-full"></div>
                </div>
              </div>
            </div>
          </section>

          <section class="p-4 bg-white border-b border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
            <UCIJuninPie :uciInfo="juninUCIInfo" />
            <UCIPeruPie :uciInfo="uciInfo" />
          </section>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import Map from 'ol/Map';
import View from 'ol/View';
import Feature from 'ol/Feature';
import OSM from 'ol/source/OSM';
import VectorSource from 'ol/source/Vector';
import PointGeom from 'ol/geom/Point';
import { fromLonLat, transform } from 'ol/proj';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import Style from 'ol/style/Style';
import Icon from 'ol/style/Icon';
import Overlay from 'ol/Overlay';

import AppLayout from "@/Layouts/AppLayout";
import UCIJuninPie from "@/Components/Charts/UCIJuninPie";
import UCIPeruPie from "@/Components/Charts/UCIPeruPie";

export default {
  components: {
    AppLayout,
    UCIJuninPie,
    UCIPeruPie,
  },
  props: {
    uciInfo: Object,
    hospitals: Array,
  },
  data() {
    return {
      juninUCIInfo: this.uciInfo.departaments.find(d => d.department === 'JUNIN'),
      initialLon: parseFloat(-11.641952),
      initialLat: parseFloat(-74.945649),
      map: null,
    }
  },
  methods: {
    addMapPoints() {
      const featuresArr = this.hospitals.map(hospital => {
        const feature = new Feature({
          geometry: new PointGeom(
            transform([parseFloat(hospital.latitude), parseFloat(hospital.longitude)], 'EPSG:4326', 'EPSG:3857')
          ),
        })

        const uciInfo = this.juninUCIInfo.hospitals.find(h => h.hospital.includes(hospital.name))
        const uciBeds = uciInfo ? {
          has_uci_beds: true,
          uci_available: uciInfo.uci_available,
          uci_total: uciInfo.uci_total,
        } : {
          has_uci_beds: false,
          uci_available: 0,
          uci_total: 0,
        }

        const _iconURL = uciBeds.has_uci_beds ?
        'https://raw.githubusercontent.com/do-community/travellist-laravel-demo/main/public/img/marker_visited.png' :
        'https://raw.githubusercontent.com/do-community/travellist-laravel-demo/main/public/img/marker_togo.png'

        feature.setProperties({ ...hospital, ...uciBeds })
        feature.setId(hospital.id)
        feature.setStyle(new Style({
          image: new Icon({
            anchor: [0.5, 46],
            anchorXUnits: 'fraction',
            anchorYUnits: 'pixels',
            opacity: 0.75,
            src: _iconURL,
          }),
        }))

        return feature
      })

      const vectorLayer = new VectorLayer({
        source: new VectorSource({
          features: [...featuresArr],
        }),
      })

      this.map.addLayer(vectorLayer)
    },
    addPopUpOverlay() {
      const popUpOverlay = new Overlay({
        element: this.$refs['pop-up'],
        autoPan: true,
        autoPanAnimation: { duration: 250 },
      })

      const _popUpInfoEl = this.$refs['pop-up__info']

      this.map.on('singleclick', e => {
        const _feature = this.map.forEachFeatureAtPixel(e.pixel, feature => feature)

        if (_feature) {
          const _props = _feature.getProperties()

          _popUpInfoEl.innerHTML = `
            <header class="mb-1">
              <h3 class="text-indigo-800 text-sm uppercase tracking-tight">${_props.name}</h3>
              ${_props.has_uci_beds && _props.uci_available > 0
                ? '<p class="text-sm font-bold text-green-600">Camas UCI disponibles</p>'
                : '<p class="text-sm font-bold text-red-500">Camas UCI NO disponibles</p>'}
            </header>
            <section class="text-sm text-gray-800">
              <ul>
                <li>
                  Camas UCI disponibles: <span class="font-bold">${_props.uci_available}</span>
                </li>
                <li>
                  Camas UCI total: <span class="font-bold">${_props.uci_total}</span>
                </li>
              </ul>
            </section>
          `
          popUpOverlay.setPosition(e.coordinate);
        } else {
          _popUpInfoEl.innerHTML = ''
          popUpOverlay.setPosition(undefined);
        }
      })

      this.map.addOverlay(popUpOverlay)
    },
  },
  mounted() {
    this.map = new Map({
      target: this.$refs['map-root'],
      layers: [
        new TileLayer({
          source: new OSM(),
        })
      ],
      view: new View({
        center: fromLonLat([this.initialLat, this.initialLon]),
        zoom: 8,
        constrainResolution: true,
      })
    })

    this.addMapPoints()
    this.addPopUpOverlay()
  }
};
</script>
