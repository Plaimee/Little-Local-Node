const { Router } = require("express");
const { getOutputController } = require("../controllers/outputs/getOutput.controller");

const router = Router();

router.get("/:noId", getOutputController);

module.exports = router;