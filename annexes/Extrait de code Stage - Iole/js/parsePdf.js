"use strict";

function applyValue(_value) {
  for (var id in _value) {
    if (_value.hasOwnProperty(id)) {
      document.querySelector("#".concat(id)).setAttribute('value', _value[id]);
    }
  }
}