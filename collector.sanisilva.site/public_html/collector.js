

(function () {
  let sessionId = sessionStorage.getItem("_cid_session");
  if (!sessionId) {
    sessionId = Date.now().toString(36) + Math.random().toString(36).slice(2, 9);
    sessionStorage.setItem("_cid_session", sessionId);
  }

  const ENDPOINT = "https://collector.sanisilva.site/cgi-bin/collect.py";

  
  function throttle(fn, delay) {
    let last = 0;
    return function (...args) {
      const now = Date.now();
      if (now - last >= delay) {
        last = now;
        fn.apply(this, args);
      }
    };
  }

  
  function send(payload) {
    payload.session_id = sessionId;
    payload.page = window.location.href;
    payload.ts = Date.now();

    const body = JSON.stringify(payload);
    if (navigator.sendBeacon) {
      navigator.sendBeacon(ENDPOINT, new Blob([body], { type: "application/json" }));
    } else {
      fetch(ENDPOINT, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body,
        keepalive: true,
      }).catch(() => {});
    }
  }

 
  function collectStatic() {
    const testEl = document.createElement("div");
    testEl.style.cssText = "display:flex";
    const cssEnabled = testEl.style.length > 0;

    const imgEnabled = (function () {
      try {
        const c = document.createElement("canvas");
        return c.toDataURL("image/png").indexOf("data:image/png") === 0;
      } catch (e) { return false; }
    })();

    send({
      type: "static",
      user_agent: navigator.userAgent,
      language: navigator.language,
      cookies_enabled: navigator.cookieEnabled,
      js_enabled: true,
      images_enabled: imgEnabled,
      css_enabled: cssEnabled,
      screen_width: screen.width,
      screen_height: screen.height,
      window_width: window.innerWidth,
      window_height: window.innerHeight,
      connection_type: navigator.connection ? navigator.connection.effectiveType : "unknown",
    });
  }

  function collectPerformance() {
    const timing = performance.timing;
    const totalLoadTime = timing.loadEventEnd - timing.navigationStart;
    send({
      type: "performance",
      page_load_start: timing.navigationStart,
      page_load_end: timing.loadEventEnd,
      total_load_time_ms: totalLoadTime,
      timing_object: {
        navigation_start: timing.navigationStart,
        response_end: timing.responseEnd,
        dom_interactive: timing.domInteractive,
        dom_complete: timing.domComplete,
        load_event_end: timing.loadEventEnd,
      },
    });
  }

  const IDLE_THRESHOLD_MS = 2000;
  let idleTimer = null;
  let idleStart = null;

  function resetIdle() {
    if (idleTimer) clearTimeout(idleTimer);
    if (idleStart !== null) {
      send({
        type: "activity",
        event: "idle_end",
        idle_end: Date.now(),
        idle_duration_ms: Date.now() - idleStart,
      });
      idleStart = null;
    }
    idleTimer = setTimeout(function () {
      idleStart = Date.now();
      send({ type: "activity", event: "idle_start", idle_start: idleStart });
    }, IDLE_THRESHOLD_MS);
  }

  document.addEventListener("mousemove", throttle(function (e) {
    resetIdle();
    send({ type: "activity", event: "mousemove", x: e.clientX, y: e.clientY });
  }, 1000));

  document.addEventListener("scroll", throttle(function () {
    resetIdle();
    send({ type: "activity", event: "scroll", scroll_x: window.scrollX, scroll_y: window.scrollY });
  }, 1000));

  document.addEventListener("click", function (e) {
    resetIdle();
    send({ type: "activity", event: "click", x: e.clientX, y: e.clientY, button: e.button });
  });

  document.addEventListener("keydown", throttle(function (e) {
    resetIdle();
    send({ type: "activity", event: "keydown", key: e.key, code: e.code });
  }, 500));

  document.addEventListener("keyup", throttle(function (e) {
    resetIdle();
    send({ type: "activity", event: "keyup", key: e.key, code: e.code });
  }, 500));

  send({ type: "activity", event: "page_enter", time: Date.now() });

  window.addEventListener("beforeunload", function () {
    send({ type: "activity", event: "page_leave", time: Date.now() });
  });

  window.onerror = function (message, source, lineno, colno, error) {
    send({
      type: "activity",
      event: "js_error",
      message: message,
      source: source,
      lineno: lineno,
      colno: colno,
      stack: error ? error.stack : null,
    });
  };

  window.addEventListener("unhandledrejection", function (e) {
    send({ type: "activity", event: "unhandled_rejection", reason: String(e.reason) });
  });

  window.addEventListener("load", function () {
    collectStatic();
    setTimeout(collectPerformance, 0);
    resetIdle();
  });

})();
