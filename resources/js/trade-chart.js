import { init } from 'klinecharts'

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('chart_container')
    if (!el) return
console.log("32423423");
    const chart = init('chart_container', { grid: { show: false}})
    const styles = {
  grid: {
    show: false,
    horizontal: {
      show: false,
      size: 1,
      color: '#EDEDED',
      style: 'dashed',
      dashedValue: [2, 2]
    },
    vertical: {
      show: false,
      size: 1,
      color: '#EDEDED',
      style: 'dashed',
      dashedValue: [2, 2]
    }
  },
  candle: {
    // 'candle_solid' | 'candle_stroke' | 'candle_up_stroke' | 'candle_down_stroke' | 'ohlc' | 'area'
    type: 'candle_solid',
    bar: {
      // 'current_open' | 'previous_close'
      compareRule: 'current_open',
      upColor: '#2DC08E',
      downColor: '#F92855',
      noChangeColor: '#888888',
      upBorderColor: '#2DC08E',
      downBorderColor: '#F92855',
      noChangeBorderColor: '#888888',
      upWickColor: '#2DC08E',
      downWickColor: '#F92855',
      noChangeWickColor: '#888888'
    },
    area: {
      lineSize: 2,
      lineColor: '#2196F3',
      smooth: false,
      value: 'close',
      backgroundColor: [{
        offset: 0,
        color: 'rgba(33, 150, 243, 0.01)'
      }, {
        offset: 1,
        color: 'rgba(33, 150, 243, 0.2)'
      }],
      point: {
        show: true,
        color: '#2196F3',
        radius: 4,
        rippleRadius: 8,
        animation: true,
        animationDuration: 1000
      }
    },
    priceMark: {
      show: true,
      high: {
        show: true,
        color: '#D9D9D9',
        textMargin: 5,
        textSize: 10,
        textFamily: 'Helvetica Neue',
        textWeight: 'normal'
      },
      low: {
        show: true,
        color: '#D9D9D9',
        textMargin: 5,
        textSize: 10,
        textFamily: 'Helvetica Neue',
        textWeight: 'normal',
      },
      last: {
        show: true,
        // 'current_open' | 'previous_close'
        compareRule: 'current_open',
        upColor: '#2DC08E',
        downColor: '#F92855',
        noChangeColor: '#888888',
        line: {
          show: true,
          // 'solid' | 'dashed'
          style: 'dashed',
          dashedValue: [4, 4],
          size: 1
        },
        text: {
          show: true,
          // 'fill' | 'stroke' | 'stroke_fill'
          style: 'fill',
          size: 12,
          paddingLeft: 4,
          paddingTop: 4,
          paddingRight: 4,
          paddingBottom: 4,
          // 'solid' | 'dashed'
          borderStyle: 'solid',
          borderSize: 0,
          borderColor: 'transparent',
          borderDashedValue: [2, 2],
          color: '#FFFFFF',
          family: 'Helvetica Neue',
          weight: 'normal',
          borderRadius: 2
        },
        
        extendTexts: []
      }
    },
  },
 
}

    
    const data = Array.from({ length: 50 }, (_, i) => {
        const timestamp = 1713744000000 + i * 60 * 1000
        const open = 60000 + i * 10
        const close = open + (Math.random() - 0.5) * 100
        const high = Math.max(open, close) + 50
        const low = Math.min(open, close) - 50
        const volume = 1000 + Math.random() * 500

        return {
            timestamp,
            open,
            high,
            low,
            close,
            volume
        }
    })

    chart.setSymbol({ ticker: 'TestSymbol' })
        chart.setPeriod({ span: 1, type: 'day' })
        chart.setDataLoader({
          getBars: ({ callback}) => {
            callback(data)
          }
        })
    chart.setStyles(styles);
    window.addEventListener('resize', () => {
        chart.resize()
    })
})