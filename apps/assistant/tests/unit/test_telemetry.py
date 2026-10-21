from unittest.mock import MagicMock, patch

from app.telemetry import setup_telemetry


def test_setup_telemetry_skips_when_no_endpoint() -> None:
    with patch("app.telemetry.settings") as s:
        s.otel_exporter_otlp_endpoint = None

        setup_telemetry()


def test_setup_telemetry_configures_provider() -> None:
    mock_provider = MagicMock()
    mock_processor = MagicMock()
    mock_exporter = MagicMock()

    with (
        patch("app.telemetry.settings") as s,
        patch("app.telemetry.TracerProvider", return_value=mock_provider),
        patch("app.telemetry.BatchSpanProcessor", return_value=mock_processor),
        patch("app.telemetry.OTLPSpanExporter", return_value=mock_exporter),
        patch("app.telemetry.trace") as mock_trace,
        patch("app.telemetry.HTTPXClientInstrumentor") as mock_httpx,
        patch("app.telemetry.RedisInstrumentor") as mock_redis,
    ):
        s.otel_exporter_otlp_endpoint = "http://otel:4318"
        s.otel_service_name = "assistant"

        setup_telemetry()

    mock_provider.add_span_processor.assert_called_once_with(mock_processor)
    mock_trace.set_tracer_provider.assert_called_once_with(mock_provider)
    mock_httpx.return_value.instrument.assert_called_once()
    mock_redis.return_value.instrument.assert_called_once()
