/**
 * resources/js/trading-chart/chart-config.js
 *
 * Centralised KLineCharts style config and chart constants.
 * Edit here to restyle all chart instances across the app.
 */

export const CHART_COLORS = {
    bg:          '#080c12',
    surface:     '#0d1420',
    border:      '#1c2d44',
    borderBright:'#243b58',
    text:        '#c8d8ea',
    textDim:     '#4e6680',
    accent:      '#00c9ff',
    green:       '#00e676',
    red:         '#ff3d5a',
    neutral:     '#4e6680',
};

export const CHART_STYLES = {
    grid: {
        horizontal: {
            color:       CHART_COLORS.border,
            style:       'dashed',
            dashedValue: [2, 2],
        },
        vertical: {
            color:       CHART_COLORS.border,
            style:       'dashed',
            dashedValue: [2, 2],
        },
    },
    candle: {
        bar: {
            upColor:             CHART_COLORS.green,
            downColor:           CHART_COLORS.red,
            noChangeColor:       CHART_COLORS.neutral,
            upBorderColor:       CHART_COLORS.green,
            downBorderColor:     CHART_COLORS.red,
            noChangeBorderColor: CHART_COLORS.neutral,
            upWickColor:         CHART_COLORS.green,
            downWickColor:       CHART_COLORS.red,
            noChangeWickColor:   CHART_COLORS.neutral,
        },
        priceMark: {
            high: {
                show:       true,
                color:      CHART_COLORS.textDim,
                textSize:   10,
            },
            low: {
                show:       true,
                color:      CHART_COLORS.textDim,
                textSize:   10,
            },
            last: {
                show:         true,
                upColor:      CHART_COLORS.green,
                downColor:    CHART_COLORS.red,
                noChangeColor:CHART_COLORS.neutral,
                line: {
                    show:        true,
                    style:       'dashed',
                    dashedValue: [4, 4],
                    size:        1,
                },
                text: {
                    show:          true,
                    size:          11,
                    paddingLeft:   4,
                    paddingRight:  4,
                    paddingTop:    2,
                    paddingBottom: 2,
                    borderRadius:  2,
                },
            },
        },
    },
    xAxis: {
        axisLine: { show: true,  color: CHART_COLORS.border, size: 1 },
        tickLine: { show: false },
        tickText: { show: true,  color: CHART_COLORS.textDim, size: 10 },
    },
    yAxis: {
        axisLine: { show: false },
        tickLine: { show: false },
        tickText: { show: true, color: CHART_COLORS.textDim, size: 10 },
    },
    crosshair: {
        horizontal: {
            line: {
                show:        true,
                color:       CHART_COLORS.textDim,
                style:       'dashed',
                dashedValue: [4, 4],
            },
            text: {
                show:            true,
                size:            11,
                color:           CHART_COLORS.text,
                backgroundColor: CHART_COLORS.surface,
                borderColor:     CHART_COLORS.borderBright,
                paddingLeft:     6,
                paddingRight:    6,
                paddingTop:      2,
                paddingBottom:   2,
                borderRadius:    2,
            },
        },
        vertical: {
            line: {
                show:        true,
                color:       CHART_COLORS.textDim,
                style:       'dashed',
                dashedValue: [4, 4],
            },
            text: {
                show:            true,
                size:            11,
                color:           CHART_COLORS.text,
                backgroundColor: CHART_COLORS.surface,
                borderColor:     CHART_COLORS.borderBright,
                paddingLeft:     6,
                paddingRight:    6,
                paddingTop:      2,
                paddingBottom:   2,
                borderRadius:    2,
            },
        },
    },
};
